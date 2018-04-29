<?php

namespace App\Controller\Connected\Map;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SectorController extends Controller
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

        $fleetIn = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.sector = :id')
            ->andWhere('p.sector != :id')
            ->setParameters(array('id' => $id))
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        $fleetOut = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.sector != :id')
            ->andWhere('p.sector = :id')
            ->setParameters(array('id' => $id))
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        $fleetCurrent = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.sector = :id')
            ->andWhere('p.sector = :id')
            ->setParameters(array('id' => $id))
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/sector.html.twig', [
            'planets' => $planets,
            'usePlanet' => $usePlanet,
            'id' => $id,
            'fleetIn' => $fleetIn,
            'fleetOut' => $fleetOut,
            'fleetCurrent' => $fleetCurrent,
        ]);
    }

    /**
     * @Route("/flotte-orbite/{id}/{idp}", name="fleet_sector", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function fleetAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('p.id = :id')
            ->setParameters(array('id' => $id))
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/fleet.html.twig', [
            'fleets' => $fleets,
            'usePlanet' => $usePlanet,
        ]);
    }
}