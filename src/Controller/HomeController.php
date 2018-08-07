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
        $connected = new DateTime();
        $connected->setTimezone(new DateTimeZone('Europe/Paris'));
        $connected->sub(new DateInterval('PT' . 1800 . 'S'));

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(array('user' => $this->getUser()->getUsername()))
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
                $usePlanet = null;
        }

        $allUsers = $em->getRepository('App:User')
                        ->createQueryBuilder('u')
                        ->select('count(u)')
                        ->getQuery()
                        ->getSingleScalarResult();

        $userCos = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.lastActivity > :date')
            ->setParameters(array('date' => $connected))
            ->getQuery()
            ->getResult();

        $userCos = count($userCos);
        if(!$userCos) {
            $userCos = 1;
        }

        return $this->render('index.html.twig', [
            'allUsers' => $allUsers,
            'usePlanet' => $usePlanet,
            'userCos' => $userCos,
        ]);
    }
}
