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
     * @Route("/carte-spatial/{id}/{idp}", name="map", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function mapAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

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
            'usePlanet' => $usePlanet,
        ]);
    }
}