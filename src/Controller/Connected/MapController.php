<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MapController extends Controller
{
    /**
     * @Route("/carte-spatial/{id}", name="map", requirements={"id"="\d+"})
     */
    public function mapAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->join('p.sector', 's')
            ->where('s.position = :id')
            ->setParameter('id', $id)
            ->orderBy('p.position')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map.html.twig', [
            'planets' => $planets,
        ]);
    }
}