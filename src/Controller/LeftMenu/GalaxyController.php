<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class GalaxyController extends Controller
{
    /**
     * @Route("/galaxie", name="galaxy")
     * @Route("/galaxie/", name="galaxy_withSlash")
     */
    public function galaxyAction()
    {
        return $this->render('left_menu/galaxy.html.twig');
    }
}