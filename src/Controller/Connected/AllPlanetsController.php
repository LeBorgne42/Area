<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class AllPlanetsController extends AbstractController
{
    public function allPlanetsAction(Planet $usePlanet, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user->getOrderPlanet() == 'alpha') {
            $crit = 'p.name';
        } elseif ($user->getOrderPlanet() == 'colo') {
            $crit = 'p.nbColo';
        } else {
            $crit = 'p.id';
        }
        $order = 'ASC';

        $allPlanets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->join('p.sector', 's')
            ->join('s.galaxy', 'g')
            ->select('p.id, p.position, p.name, p.caserne, p.signature, p.bunker, p.centerSearch, p. lightUsine, p.uranium, p.empty, s.position as sector, g.position as galaxy, s.id as idSector, g.id as idGalaxy, p.construct, p.ground, p.groundPlace, p.sky, p.skyPlace, p.imageName')
            ->where('p.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy($crit, $order)
            ->getQuery()
            ->getResult();

        if ($id == 1) {
            return $this->render('menu/_right_planet.html.twig', ['allPlanets' => $allPlanets, 'usePlanet' => $usePlanet]);
        } elseif ($id == 3) {
            return $this->render('menu/_top_ressource_space.html.twig', ['allPlanets' => $allPlanets, 'usePlanet' => $usePlanet]);
        } elseif ($id == 4) {
            return $this->render('menu/_top_ressource.html.twig', ['allPlanets' => $allPlanets, 'usePlanet' => $usePlanet]);
        } elseif ($id == 2) {
            return $this->render('menu/_top_ressource_building.html.twig', ['allPlanets' => $allPlanets, 'usePlanet' => $usePlanet]);
        }
        return $this->render('menu/_right_planet.html.twig', ['allPlanets' => $allPlanets, 'usePlanet' => $usePlanet]);
    }
}