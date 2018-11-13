<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;
use DateTimeZone;
use DateInterval;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $lastActivity = new DateTime();
        $lastActivity->setTimezone(new DateTimeZone('Europe/Paris'));
        $lastActivity->sub(new DateInterval('PT' . 1800 . 'S'));

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

        return $this->render('index.html.twig', [
            'allUsers' => $nbrUsers,
            'usePlanet' => $usePlanet,
            'userCos' => $nbrUsersConnected,
        ]);
    }
}
