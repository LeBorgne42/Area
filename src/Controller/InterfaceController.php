<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Page controller.
 *
 * @Route("/user")
 * @Security("has_role('ROLE_USER')")
 */
class InterfaceController extends Controller
{
    /**
     * @Route("/interface", name="interface")
     * @Route("/interface/", name="interface_withSlash")
     */
    public function index()
    {
        return $this->render('interface/index.html.twig', [
            'controller_name' => 'InterfaceController',
        ]);
    }
}
