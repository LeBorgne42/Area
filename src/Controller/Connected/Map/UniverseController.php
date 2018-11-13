<?php

namespace App\Controller\Connected\Map;

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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/universe.html.twig', [
            'galaxys' => $galaxys,
            'usePlanet' => $usePlanet,
        ]);
    }
}