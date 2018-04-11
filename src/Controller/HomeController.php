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
        $allUsers = $em->getRepository('App:User')
                        ->createQueryBuilder('u')
                        ->select('count(u)')
                        ->getQuery()
                        ->getSingleScalarResult();

        return $this->render('index.html.twig', [
            'allUsers' => $allUsers,
        ]);
    }
}
