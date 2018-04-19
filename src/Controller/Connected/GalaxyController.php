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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/galaxy.html.twig', [
            'sectors' => $sectors,
            'usePlanet' => $usePlanet,
        ]);
    }
}