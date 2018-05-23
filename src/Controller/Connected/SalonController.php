<?php

namespace App\Controller\Connected;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SalonType;
use App\Entity\S_Content;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SalonController extends Controller
{
    /**
     * @Route("/salon/{idp}", name="salon", requirements={"idp"="\d+"})
     * @Route("/salon/{idp}/{id}", name="salon_id", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function salonAction(Request $request, $idp, $id = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
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

        if($user->getAlly()) {
            $sigle = $user->getAlly()->getSigle();
        } else {
            $sigle = 'AKOUNAMATATA';
        }

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameters(array('id' => $id))
            ->getQuery()
            ->getOneOrNullResult();

        $salons = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->leftJoin('s.allys', 'a')
            ->leftJoin('s.users', 'u')
            ->where('a.sigle = :sigle')
            ->orWhere('s.id = :id')
            ->orWhere('u.username = :user')
            ->setParameters(array('sigle' => $sigle, 'id' => 1, 'user' => $user->getUserName()))
            ->getQuery()
            ->getResult();

        $ok = 0;
        foreach($salons as $one) {
            if($one == $salon) {
                $ok = 1;
            }
        }
        if($ok == 0) {
            return $this->redirectToRoute('salon', array('idp' => $usePlanet->getId()));
        }

        $form_message = $this->createForm(SalonType::class);
        $form_message->handleRequest($request);

        if($request->isXmlHttpRequest()) {
            $newMessages = $em->getRepository('App:S_Content')
                ->createQueryBuilder('sc')
                ->orderBy('sc.sendAt', 'ASC')
                ->where('sc.salon = :attachSalon')
                ->andWhere('sc.user != :user')
                ->andWhere('sc.sendAt > :date')
                ->setParameters(array('attachSalon' => $salon, 'user' => $user, 'date' => $user->getSalonAt()))
                ->setMaxResults('1')
                ->getQuery()
                ->getResult();

            if($newMessages || $user->getSalonAt() == null) {
                $response = new JsonResponse();
                $response->setData(
                    array(
                        'has_error' => false,
                    )
                );
                $user->setSalonAt($now);
                $em->persist($user);
                $em->flush();
                return $response;
            } else {
                $response = new JsonResponse();
                $response->setData(
                    array(
                        'has_error' => true,
                    )
                );
                return $response;
            }
        }

        if ($form_message->isSubmitted() && $form_message->isValid() && ($user->getSalonBan() > $now || $user->getSalonBan() == null)) {
            $message = new S_Content();
            $message->setSalon($salon);
            $message->setMessage(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $message->setUser($user);

            if(count($salon->getContents()) > 50) {
                $removeMessage = $em->getRepository('App:S_Content')
                    ->createQueryBuilder('sc')
                    ->orderBy('sc.sendAt', 'ASC')
                    ->where('sc.salon = :attachSalon')
                    ->setParameters(array('attachSalon' => $salon))
                    ->setMaxResults('10')
                    ->getQuery()
                    ->getResult();
                foreach($removeMessage as $oneMessage) {
                    $em->remove($oneMessage);
                }
            }
            $userViews = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.id != :user')
                ->setParameters(array('user' => $user->getId()))
                ->getQuery()
                ->getResult();
            foreach($userViews as $userView) {
                $userView->setSalonAt(null);
                $em->persist($userView);
            }

            $em->persist($message);

            $form_message = null;
            $form_message = $this->createForm(SalonType::class);
        }
        $user->setSalonAt($now);
        $em->persist($user);
        $em->flush();

        return $this->render('connected/salon.html.twig', [
            'usePlanet' => $usePlanet,
            'salons' => $salons,
            'salon' => $salon,
            'form_message' => $form_message->createView(),
        ]);
    }

    /**
     * @Route("/rejoindre-salon/{sigle}/{idp}", name="ally_join_salon", requirements={"sigle"="\w+", "idp"="\d+"})
     */
    public function addSalonAction($sigle, $idp)
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

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameter('name', "Ambassade - " . $sigle)
            ->getQuery()
            ->getOneOrNullResult();

        $salon->addUser($user);
        $em->persist($salon);

        $em->flush();

        return $this->redirectToRoute('salon', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/quitter-salon/{id}/{idp}", name="ally_leave_salon", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function leaveSalonAction($id, $idp)
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

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $salon->removeUser($user);
        $em->persist($salon);

        $em->flush();

        return $this->redirectToRoute('salon', array('idp' => $usePlanet->getId()));
    }
}