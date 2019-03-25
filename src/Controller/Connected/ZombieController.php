<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Planet;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ZombieController extends AbstractController
{
    /**
     * @Route("/zombie/{usePlanet}", name="zombie", requirements={"usePlanet"="\d+"})
     */
    public function soldierAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/zombie.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
}