<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class SalonController extends Controller
{
    /**
     * @Route("/salon", name="salon")
     * @Route("/salon/", name="salon_withSlash")
     */
    public function salonAction()
    {
        return $this->render('left_menu/salon.html.twig');
    }
}