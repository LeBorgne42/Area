<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class AllyController extends Controller
{
    /**
     * @Route("/alliance", name="ally")
     * @Route("/alliance/", name="ally_withSlash")
     */
    public function allyAction()
    {
        return $this->render('connected/ally.html.twig');
    }
}