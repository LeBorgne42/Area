<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MessageType;
use App\Form\Front\MessageRespondeType;
use App\Entity\Message;
use DateTime;
use DateTimeZone;
use DateInterval;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MessageController extends Controller
{
    /**
     * @Route("/message/{idp}", name="message", requirements={"idp"="\d+"})
     */
    public function messageAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $message = new Message();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $nowDel = new DateTime();
        $nowDel->setTimezone(new DateTimeZone('Europe/Paris'));
        $nowDel->sub(new DateInterval('PT' . 1209600 . 'S'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $removeMessages = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->where('m.sendAt < :now')
            ->setParameters(array('now' => $nowDel))
            ->getQuery()
            ->getResult();

        if($removeMessages) {
            foreach($removeMessages as $removeMessage) {
                $em->remove($removeMessage);
            }
        }
        $em->flush();

        $messages = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->where('m.idSender = :id')
            ->setParameters(array('id' => $user->getId()))
            ->orderBy('m.sendAt', 'DESC')
            ->getQuery()
            ->getResult();

        $messagesR = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->where('m.user = :user')
            ->setParameters(array('user' => $user))
            ->orderBy('m.sendAt', 'DESC')
            ->getQuery()
            ->getResult();

        $user->setViewMessage(true);
        $em->persist($user);
        $em->flush();

        $form_message = $this->createForm(MessageType::class, $message);
        $form_message->handleRequest($request);

        if ($form_message->isSubmitted() && $form_message->isValid() && abs($form_message->get('bitcoin')->getData()) < $user->getBitcoin() &&
            ($user->getSalonBan() > $now || $user->getSalonBan() == null)) {
            $recever = $form_message->get('user')->getData();
            if ($form_message->get('anonymous')->getData() == false) {
                $message->setSender($user->getUsername());
            }
            $message->setIdSender($user->getId());
            $message->setContent(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $recever->setBitcoin($recever->getBitcoin() + abs($form_message->get('bitcoin')->getData()));
            $recever->setViewMessage(false);
            $user->setBitcoin($user->getBitcoin() - abs($form_message->get('bitcoin')->getData()));
            $em->persist($user);
            $em->persist($recever);
            $em->persist($message);
            $em->flush();

            $form_message = null;
            $form_message = $this->createForm(MessageType::class);
        }

        return $this->render('connected/message.html.twig', [
            'usePlanet' => $usePlanet,
            'messages' => $messages,
            'messagesR' => $messagesR,
            'form_message' => $form_message->createView(),
        ]);
    }

    /**
     * @Route("/repondre/{idp}/{id}", name="message_responde", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function messageRespondeAction(Request $request, $idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $message = new Message();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        if($user->getSalonBan() > $now) {
            return $this->redirectToRoute('message', array('idp' => $usePlanet->getId()));
        }

        $form_message = $this->createForm(MessageRespondeType::class, $message);
        $form_message->handleRequest($request);

        if ($form_message->isSubmitted() && $form_message->isValid() && abs($form_message->get('bitcoin')->getData()) < $user->getBitcoin()) {
            $userRecever = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.id = :id')
                ->setParameters(array('id' => $id))
                ->getQuery()
                ->getOneOrNullResult();

            if ($form_message->get('anonymous')->getData() == false) {
                $message->setSender($user->getUsername());
            }
            $message->setIdSender($user->getId());
            $message->setUser($userRecever);
            $message->setContent(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $userRecever->setBitcoin($userRecever->getBitcoin() + abs($form_message->get('bitcoin')->getData()));
            $userRecever->setViewMessage(false);
            $user->setBitcoin($user->getBitcoin() - abs($form_message->get('bitcoin')->getData()));
            $em->persist($user);
            $em->persist($userRecever);
            $em->persist($message);
            $em->flush();
            return $this->redirectToRoute('message', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/profil/user_responde.html.twig', [
            'usePlanet' => $usePlanet,
            'form_message' => $form_message->createView(),
            'id' => $id,
        ]);
    }

    /**
     * @Route("/message-view/{idp}/{id}", name="message_view", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function messageViewAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $message = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->where('m.id = :id')
            ->andWhere('m.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $message->setNewMessage(false);
        $em->persist($message);
        $em->flush();

        return $this->redirectToRoute('message', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/message-view-all/{idp}/", name="message_all_view", requirements={"idp"="\d+"})
     */
    public function messageAllViewAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $messages = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->where('m.newMessage = :true')
            ->andWhere('m.user = :user')
            ->setParameters(array('true' => true, 'user' => $user))
            ->getQuery()
            ->getResult();

        foreach($messages as $message) {
            $message->setNewMessage(false);
            $em->persist($message);
        }
        $em->flush();

        return $this->redirectToRoute('message', array('idp' => $usePlanet->getId()));
    }
}