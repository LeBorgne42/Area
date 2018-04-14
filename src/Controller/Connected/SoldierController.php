<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
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
        return $this->render('connected/soldier.html.twig');
    }
}