<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class DailyCostController extends AbstractController
{
    /**
     * @Route("/aides/{usePlanet}", name="help_new", requirements={"usePlanet"="\d+"})
     */
    public function helpNewAction(Planet $usePlanet)
    {
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/help_new.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
}