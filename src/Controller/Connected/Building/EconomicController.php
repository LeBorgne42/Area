<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class EconomicController extends Controller
{
    /**
     * @Route("/contruire-laboratoire/{idp}", name="building_add_search", requirements={"idp"="\d+"})
     */
    public function buildingAddSearchAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $search = $usePlanet->getBuilding()->getBuildSearch();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $search->getGround();
        if(($usePlanetNb < $search->getNiobium() || $usePlanetWt < $search->getWater()) ||
            ($search->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.5;
        $cost = 2.5;
        $time = 3;
        $multiple = $search->getLevel() / 5;
        if($search->getLevel() % 3 == 0 && $search->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
            $prod = $prod - (0.02 * $multiple);
        }
        $now->add(new DateInterval('PT' . $search->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $search->getNiobium());
        $usePlanet->setWater($usePlanetWt - $search->getWater());
        $usePlanet->setGroundPlace($newGround);
        $search->setNiobium($search->getNiobium() * $cost);
        $search->setWater($search->getWater() * $cost);
        $search->setProduction($search->getProduction() + $prod);
        $search->setLevel($search->getLevel() + 1);
        $search->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $search->setConstructTime($search->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($search);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-laboratoire/{idp}", name="building_remove_search", requirements={"idp"="\d+"})
     */
    public function buildingRemoveSearchAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $search = $usePlanet->getBuilding()->getBuildSearch();
        $newGround = $usePlanet->getGroundPlace() - $search->getGround();
        if($search->getLevel() == 0 || $search->getFinishAt() > $now || ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.5;
        $cost = 2.5;
        $time = 3;
        $multiple = $search->getLevel() / 5;
        if($search->getLevel() % 3 == 0 && $search->getLevel() < 12) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.3 * $multiple);
            $prod = $prod + (0.075 * $multiple);
        }
        $now->add(new DateInterval('PT' . $search->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $search->setNiobium($search->getNiobium() / $cost);
        $search->setWater($search->getWater() / $cost);
        $search->setProduction($search->getProduction() - $prod);
        $search->setLevel($search->getLevel() - 1);
        $search->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $search->setConstructTime($search->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($search->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($search->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($search);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-ville/{idp}", name="building_add_city", requirements={"idp"="\d+"})
     */
    public function buildingAddCityAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $city = $usePlanet->getBuilding()->getCity();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $city->getGround();
        if(($usePlanetNb < $city->getNiobium() || $usePlanetWt < $city->getWater()) ||
            ($city->getFinishAt() > $now || $newGround > $usePlanet->getGround() || $user->getResearch()->getDemography()->getLevel() == 0) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.25;
        $cost = 2.5;
        $time = 2;
        $multiple = $city->getLevel() / 5;
        if($city->getLevel() % 3 == 0 && $city->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
            $prod = $prod - (0.02 * $multiple);
        }
        $now->add(new DateInterval('PT' . $city->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $city->getNiobium());
        $usePlanet->setWater($usePlanetWt - $city->getWater());
        $usePlanet->setGroundPlace($newGround);
        $city->setNiobium($city->getNiobium() * $cost);
        $city->setWater($city->getWater() * $cost);
        $city->setProduction($city->getProduction() + $prod);
        $city->setLevel($city->getLevel() + 1);
        $city->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $city->setConstructTime($city->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($city);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-ville/{idp}", name="building_remove_city", requirements={"idp"="\d+"})
     */
    public function buildingRemoveCityAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $city = $usePlanet->getBuilding()->getCity();
        $newGround = $usePlanet->getGroundPlace() - $city->getGround();
        if($city->getLevel() == 0 || $city->getFinishAt() > $now ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.25;
        $cost = 2.5;
        $time = 2;
        $multiple = $city->getLevel() / 5;
        if($city->getLevel() % 3 == 0 && $city->getLevel() < 12) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.3 * $multiple);
            $prod = $prod + (0.075 * $multiple);
        }
        $now->add(new DateInterval('PT' . $city->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $city->setNiobium($city->getNiobium() / $cost);
        $city->setWater($city->getWater() / $cost);
        $city->setProduction($city->getProduction() - $prod);
        $city->setLevel($city->getLevel() - 1);
        $city->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $city->setConstructTime($city->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($city->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($city->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($city);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
    
    /**
     * @Route("/contruire-metropole/{idp}", name="building_add_metropole", requirements={"idp"="\d+"})
     */
    public function buildingAddMetropoleAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $metropole = $usePlanet->getBuilding()->getMetropole();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $metropole->getGround();
        $newSky = $usePlanet->getSkyPlace() + $metropole->getSky();
        if(($usePlanetNb < $metropole->getNiobium() || $usePlanetWt < $metropole->getWater()) ||
            ($metropole->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($newSky > $usePlanet->getSky() || $user->getResearch()->getDemography()->getLevel() < 5) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1;
        $cost = 2.5;
        $time = 3;
        $multiple = $metropole->getLevel() / 5;
        if($metropole->getLevel() % 3 == 0 && $metropole->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
            $prod = $prod - (0.02 * $multiple);
        }
        $now->add(new DateInterval('PT' . $metropole->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $metropole->getNiobium());
        $usePlanet->setWater($usePlanetWt - $metropole->getWater());
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $metropole->setNiobium($metropole->getNiobium() * $cost);
        $metropole->setWater($metropole->getWater() * $cost);
        $metropole->setProduction($metropole->getProduction() + $prod);
        $metropole->setLevel($metropole->getLevel() + 1);
        $metropole->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $metropole->setConstructTime($metropole->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($metropole);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-metropole/{idp}", name="building_remove_metropole", requirements={"idp"="\d+"})
     */
    public function buildingRemoveMetropoleAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $metropole = $usePlanet->getBuilding()->getMetropole();
        $newGround = $usePlanet->getGroundPlace() - $metropole->getGround();
        $newSky = $usePlanet->getSkyPlace() - $metropole->getSky();
        if($metropole->getLevel() == 0 || $metropole->getFinishAt() > $now ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1;
        $cost = 2.5;
        $time = 3;
        $multiple = $metropole->getLevel() / 5;
        if($metropole->getLevel() % 3 == 0 && $metropole->getLevel() < 12) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.3 * $multiple);
            $prod = $prod + (0.075 * $multiple);
        }
        $now->add(new DateInterval('PT' . $metropole->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $metropole->setNiobium($metropole->getNiobium() / $cost);
        $metropole->setWater($metropole->getWater() / $cost);
        $metropole->setProduction($metropole->getProduction() - $prod);
        $metropole->setLevel($metropole->getLevel() - 1);
        $metropole->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $metropole->setConstructTime($metropole->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($metropole->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($metropole->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $em->persist($usePlanet);
        $em->persist($metropole);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}