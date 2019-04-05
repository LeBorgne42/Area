<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class EconomicController extends AbstractController
{
    /**
     * @Route("/contruire-laboratoire/{usePlanet}", name="building_add_search", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddSearchAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCenterSearch() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 5;

        if(($usePlanetNb < ($level * 2850) || $usePlanetWt < ($level * 3580)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 900) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 2850));
        $usePlanet->setWater($usePlanetWt - ($level * 3580));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('centerSearch');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-laboratoire/{usePlanet}", name="building_remove_search", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveSearchAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCenterSearch();
        $newGround = $usePlanet->getGroundPlace() - 5;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getScientist() > $usePlanet->getScientistMax() - 500)) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setCenterSearch($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setScientistMax($usePlanet->getScientistMax() - 500);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-ville/{usePlanet}", name="building_add_city", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddCityAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCity() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 15000) || $usePlanetWt < ($level * 11000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            $user->getDemography() == 0) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 720) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 15000));
        $usePlanet->setWater($usePlanetWt - ($level * 11000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('city');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-ville/{usePlanet}", name="building_remove_city", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveCityAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCity();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setCity($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setWorkerMax($usePlanet->getWorkerMax() - 25000);
        $usePlanet->setWorkerProduction($usePlanet->getWorkerProduction() - 2.78);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }
    
    /**
     * @Route("/contruire-metropole/{usePlanet}", name="building_add_metropole", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddMetropoleAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getMetropole() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;
        $newSky = $usePlanet->getSkyPlace() + 6;

        if(($usePlanetNb < ($level * 75000) || $usePlanetWt < ($level * 55000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDemography() < 5 || $newSky > $usePlanet->getSky())) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2800) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 75000));
        $usePlanet->setWater($usePlanetWt - ($level * 55000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('metropole');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-metropole/{usePlanet}", name="building_remove_metropole", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveMetropoleAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getMetropole();
        $newGround = $usePlanet->getGroundPlace() - 6;
        $newSky = $usePlanet->getSkyPlace() - 6;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setMetropole($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setWorkerMax($usePlanet->getWorkerMax() - 75000);
        $usePlanet->setWorkerProduction($usePlanet->getWorkerProduction() - 4.16);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }
}