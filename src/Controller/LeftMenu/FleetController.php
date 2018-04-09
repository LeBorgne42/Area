<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class FleetController extends Controller
{
    /**
     * @Route("/flotte", name="fleet")
     * @Route("/flotte/", name="fleet_withSlash")
     */
    public function dailyCostAction()
    {
        return $this->render('left_menu/fleet.html.twig');
    }
}