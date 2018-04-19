<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();

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

        return $this->render('index.html.twig', [
            'allUsers' => $allUsers,
            'usePlanet' => $usePlanet,
        ]);
    }
}
