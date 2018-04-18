<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class UniverseController extends Controller
{
    /**
     * @Route("/univers/{idp}", name="universe", requirements={"idp"="\d+"})
     */
    public function universeAction($idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/universe.html.twig', [
            'galaxys' => $galaxys,
            'usePlanet' => $usePlanet,
        ]);
    }
}