<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class ReportController extends Controller
{
    /**
     * @Route("/rapport", name="report")
     * @Route("/rapport/", name="report_withSlash")
     */
    public function reportAction()
    {
        return $this->render('connected/report.html.twig');
    }
}