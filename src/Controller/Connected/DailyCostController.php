<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
 * @Security("has_role('ROLE_USER')")
 */
class DailyCostController extends Controller
{
    /**
     * @Route("/aides/{idp}", name="help_new", requirements={"idp"="\d+"})
     */
    public function helpNewAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        return $this->render('connected/help_new.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
}