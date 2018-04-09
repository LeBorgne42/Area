<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class DailyCostController extends Controller
{
    /**
     * @Route("/entretien", name="dailyCost")
     * @Route("/entretien/", name="dailyCost_withSlash")
     */
    public function dailyCostAction()
    {
        return $this->render('left_menu/dailyCost.html.twig');
    }
}