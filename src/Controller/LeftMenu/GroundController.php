<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class GroundController extends Controller
{
    /**
     * @Route("/orbite", name="ground")
     * @Route("/orbite/", name="ground_withSlash")
     */
    public function groundAction()
    {
        return $this->render('left_menu/ground.html.twig');
    }
}