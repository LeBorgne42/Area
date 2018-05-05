<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MessageType;
use App\Entity\Message;
use DateTime;
use DateTimeZone;

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

        if ($form_message->isSubmitted() && $form_message->isValid() && $form_message->get('bitcoin')->getData() < $user->getBitcoin()) {
            $recever = $form_message->get('user')->getData();
            if ($form_message->get('anonymous')->getData() == false) {
                $message->setSender($user->getUsername());
            }
            $message->setIdSender($user->getId());
            $message->setContent(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $recever->setBitcoin($recever->getBitcoin() + $form_message->get('bitcoin')->getData());
            $recever->setViewMessage(false);
            $user->setBitcoin($user->getBitcoin() - $form_message->get('bitcoin')->getData());
            $em->persist($user);
            $em->persist($recever);
            $em->persist($message);
            $em->flush();
        }

        return $this->render('connected/message.html.twig', [
            'usePlanet' => $usePlanet,
            'messages' => $messages,
            'messagesR' => $messagesR,
            'form_message' => $form_message->createView(),
        ]);
    }

    /**
     * @Route("/repondre/{idp}", name="message_responde", requirements={"idp"="\d+"})
     */
    public function messageRespondeAction(Request $request, $idp)
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

        $form_message = $this->createForm(MessageType::class, $message);
        $form_message->handleRequest($request);

        if ($form_message->isSubmitted() && $form_message->isValid() && $form_message->get('bitcoin')->getData() < $user->getBitcoin()) {
            $recever = $form_message->get('user')->getData();
            if ($form_message->get('anonymous')->getData() == false) {
                $message->setSender($user->getUsername());
            }
            $message->setIdSender($user->getId());
            $message->setContent(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $recever->setBitcoin($recever->getBitcoin() + $form_message->get('bitcoin')->getData());
            $recever->setViewMessage(false);
            $user->setBitcoin($user->getBitcoin() - $form_message->get('bitcoin')->getData());
            $em->persist($user);
            $em->persist($recever);
            $em->persist($message);
            $em->flush();
        }

        return $this->render('connected/profil/user_responde.html.twig', [
            'usePlanet' => $usePlanet,
            'form_message' => $form_message->createView(),
        ]);
    }
}