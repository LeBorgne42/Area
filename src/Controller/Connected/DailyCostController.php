<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class DailyCostController extends AbstractController
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