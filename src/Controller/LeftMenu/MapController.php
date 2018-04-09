<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
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
        return $this->render('left_menu/map.html.twig');
    }
}