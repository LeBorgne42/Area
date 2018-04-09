<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
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
        return $this->render('left_menu/planet.html.twig');
    }
}