<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
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
        return $this->render('connected/building.html.twig');
    }
}