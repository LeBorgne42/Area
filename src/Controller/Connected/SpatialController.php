<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class SpatialController extends Controller
{
    /**
     * @Route("/chantier-spatial", name="spatial")
     * @Route("/chantier-spatial/", name="spatial_withSlash")
     */
    public function spatialAction()
    {
        return $this->render('connected/spatial.html.twig');
    }
}