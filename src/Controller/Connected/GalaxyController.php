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
     * @Route("/galaxie/{id}/{idp}", name="galaxy", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function galaxyAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/galaxy.html.twig', [
            'sectors' => $sectors,
            'usePlanet' => $usePlanet,
        ]);
    }
}