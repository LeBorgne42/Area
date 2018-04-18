<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SalonController extends Controller
{
    /**
     * @Route("/salon/{idp}", name="salon", requirements={"idp"="\d+"})
     */
    public function salonAction($idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/salon.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
}