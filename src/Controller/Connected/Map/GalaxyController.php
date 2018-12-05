<?php

namespace App\Controller\Connected\Map;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/galaxy.html.twig', [
            'sectors' => $sectors,
            'usePlanet' => $usePlanet,
        ]);
    }
}