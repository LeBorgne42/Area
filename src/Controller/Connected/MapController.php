<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MapController extends Controller
{
    /**
     * @Route("/carte-spatial", name="map")
     * @Route("/carte-spatial/", name="map_withSlash")
     */
    public function mapAction()
    {
        return $this->render('connected/map.html.twig');
    }
}