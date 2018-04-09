<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
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
        return $this->render('left_menu/report.html.twig');
    }
}