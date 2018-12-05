<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/{_locale}", name="home", defaults={"_locale" = "fr"}, requirements={"_locale" = "fr|en|de"})
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $user = $this->getUser();

        if($user) {
            if($user->getRoles()[0] == 'ROLE_PRIVATE') {
                return $this->redirectToRoute('private_home');
            }

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

        return $this->render('index.html.twig', [
            'usePlanet' => $usePlanet,
            'server' => $server
        ]);
    }
}
