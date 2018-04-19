<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SpaceShipyardController extends Controller
{
    /**
     * @Route("/contruire-chantier-spatial/{idp}", name="building_add_spaceShipyard", requirements={"idp"="\d+"})
     */
    public function buildingAddSpaceShipyardAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $spaceShip = $usePlanet->getBuilding()->getSpaceShip();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $spaceShip->getGround();
        if(($usePlanetNb < $spaceShip->getNiobium() || $usePlanetWt < $spaceShip->getWater()) || ($spaceShip->getFinishAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.1;
        $cost = 2.5;
        $time = 3;
        $multiple = $spaceShip->getLevel() / 5;
        if($spaceShip->getLevel() % 3 == 0 && $spaceShip->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
            $prod = $prod - (0.02 * $multiple);
        }
        $usePlanet->setNiobium($usePlanetNb - $spaceShip->getNiobium());
        $usePlanet->setWater($usePlanetWt - $spaceShip->getWater());
        $usePlanet->setGroundPlace($newGround);
        $spaceShip->setNiobium($spaceShip->getNiobium() * $cost);
        $spaceShip->setWater($spaceShip->getWater() * $cost);
        $spaceShip->setProduction($spaceShip->getProduction() + $prod);
        $spaceShip->setLevel($spaceShip->getLevel() + 1);
        $spaceShip->setFinishAt($now);
        $spaceShip->setConstructTime($spaceShip->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($spaceShip);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-chantier-spatial/{idp}", name="building_remove_spaceShipyard", requirements={"idp"="\d+"})
     */
    public function buildingRemoveSpaceShipyardAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $spaceShip = $usePlanet->getBuilding()->getSpaceShip();
        $newGround = $usePlanet->getGroundPlace() - $spaceShip->getGround();
        if($spaceShip->getLevel() == 0 || $spaceShip->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.1;
        $cost = 2.5;
        $time = 3;
        $multiple = $spaceShip->getLevel() / 5;
        if($spaceShip->getLevel() % 3 == 0 && $spaceShip->getLevel() < 12) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.3 * $multiple);
            $prod = $prod + (0.075 * $multiple);
        }
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $spaceShip->setNiobium($spaceShip->getNiobium() / $cost);
        $spaceShip->setWater($spaceShip->getWater() / $cost);
        $spaceShip->setProduction($spaceShip->getProduction() - $prod);
        $spaceShip->setLevel($spaceShip->getLevel() - 1);
        $spaceShip->setFinishAt($now);
        $spaceShip->setConstructTime($spaceShip->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($spaceShip->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($spaceShip->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($spaceShip);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}