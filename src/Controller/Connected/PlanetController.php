<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class PlanetController extends Controller
{
    /**
     * @Route("/planete", name="planet")
     * @Route("/planete/", name="planet_withSlash")
     */
    public function planetAction()
    {
        return $this->render('connected/planet.html.twig');
    }
}