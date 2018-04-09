<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class BuildingController extends Controller
{
    /**
     * @Route("/batiment", name="building")
     * @Route("/batiment/", name="building_withSlash")
     */
    public function buildingAction()
    {
        return $this->render('left_menu/building.html.twig');
    }
}