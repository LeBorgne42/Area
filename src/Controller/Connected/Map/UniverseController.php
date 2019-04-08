<?php

namespace App\Controller\Connected\Map;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class UniverseController extends AbstractController
{
    /**
     * @Route("/univers/{usePlanet}", name="universe", requirements={"usePlanet"="\d+"})
     */
    public function universeAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->join('g.sectors', 's')
            ->join('s.planets', 'p')
            ->join('p.user', 'u')
            ->select('g.position, count(DISTINCT u.id) as users')
            ->groupBy('g.id')
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/universe.html.twig', [
            'galaxys' => $galaxys,
            'usePlanet' => $usePlanet,
        ]);
    }
}