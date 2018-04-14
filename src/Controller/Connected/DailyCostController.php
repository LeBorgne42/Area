<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
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
        return $this->render('connected/dailyCost.html.twig');
    }
}