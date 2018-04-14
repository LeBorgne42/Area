<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class RankController extends Controller
{
    /**
     * @Route("/classement", name="rank")
     * @Route("/classement/", name="rank_withSlash")
     */
    public function rankAction()
    {
        return $this->render('connected/rank.html.twig');
    }
}