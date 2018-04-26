<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class FightController extends Controller
{
    /**
     * @Route("/weNeedABigBigFight/{id}", name="fight_war", requirements={"id"="\d+"})
     */
    public function fightAction(Request $request, $idp)
    {
        return $this->redirectToRoute('home');
    }
}