<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class GalaxyController extends Controller
{
    /**
     * @Route("/galaxie/{id}", name="galaxy", requirements={"id"="\d+"})
     */
    public function galaxyAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        return $this->render('connected/galaxy.html.twig', [
            'sectors' => $sectors,
        ]);
    }
}