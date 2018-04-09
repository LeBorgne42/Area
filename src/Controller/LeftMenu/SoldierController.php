<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class SoldierController extends Controller
{
    /**
     * @Route("/entrainement", name="soldier")
     * @Route("/entrainement/", name="soldier_withSlash")
     */
    public function soldierAction()
    {
        return $this->render('left_menu/soldier.html.twig');
    }
}