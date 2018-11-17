<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserContactType;
use DateTime;
use DateTimeZone;
use DateInterval;

class TopMenuController extends Controller
{
    /**
     * @Route("/reglement/", name="rules")
     * @Route("/reglement/", name="rules_noSlash")
     */
    public function rulesAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/rules.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
    /**
     * @Route("/medias/", name="medias")
     * @Route("/medias/", name="medias_noSlash")
     */
    public function mediasAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/medias.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
    /**
     * @Route("/but-du-jeu/", name="point_game")
     * @Route("/but-du-jeu/", name="point_game_noSlash")
     */
    public function pointGameAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/point_game.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
    /**
     * @Route("/statistiques/", name="statistics")
     * @Route("/statistiques/", name="statistics_noSlash")
     */
    public function statisticsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastActivity = new DateTime();
        $lastActivity->setTimezone(new DateTimeZone('Europe/Paris'));
        $lastActivity->sub(new DateInterval('PT' . 1800 . 'S'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        $nbrAlly = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->select('count(a)')
            ->getQuery()
            ->getSingleScalarResult();

        $nbrUsers = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->select('count(u)')
            ->getQuery()
            ->getSingleScalarResult();

        $nbrUsersConnected = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.lastActivity > :date')
            ->setParameters(['date' => $lastActivity])
            ->select('count(u)')
            ->getQuery()
            ->getSingleScalarResult();

        $nbrUsersConnected = count($nbrUsersConnected);
        if(!$nbrUsersConnected) {
            $nbrUsersConnected = 1;
        }

        return $this->render('anonymous/statistics.html.twig', [
            'usePlanet' => $usePlanet,
            'allUsers' => $nbrUsers,
            'nbrAlly' => $nbrAlly,
            'userCos' => $nbrUsersConnected,
            'server' => $server
        ]);
    }

    /**
     * @Route("/nous-contacter/", name="contact")
     * @Route("/nous-contacter/", name="contact_noSlash")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $form_contact = $this->createForm(UserContactType::class);
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted()) {
            $message = (new \Swift_Message('Reclamation joueur'))
                ->setFrom('support@areauniverse.eu')
                ->setTo('areauniverse.game@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        ['text' => $form_contact->get('text')->getData(), 'email' => $form_contact->get('email')->getData()]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash("success", "This is a success message");
            return $this->redirectToRoute('home');
        }

        return $this->render('anonymous/contact.html.twig', [
            'form_contact' => $form_contact->createView(),
        ]);
    }
}
