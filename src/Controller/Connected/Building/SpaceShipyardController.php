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
class SpaceShipyardController extends AbstractController
{
    /**
     * @Route("/construire-chantier-spatiale/{usePlanet}", name="building_add_spaceShipyard", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddSpaceShipyardAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSpaceShip() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 2;
        $newSky = $usePlanet->getSkyPlace() + 1;

        if(($usePlanetNb < ($level * 3000) || $usePlanetWt < ($level * 2000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getIndustry() == 0 || $newSky > $usePlanet->getSky())) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 3000));
        $usePlanet->setWater($usePlanetWt - ($level * 2000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('spaceShip');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-chantier-spatiale/{usePlanet}", name="building_remove_spaceShipyard", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveSpaceShipyardAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSpaceShip();
        $newGround = $usePlanet->getGroundPlace() - 2;
        $newSky = $usePlanet->getSkyPlace() - 1;

        if(($level == 0 || $usePlanet->getConstructAt() > $now)
        || $usePlanet->getProduct()) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setSpaceShip($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/construire-usine-legere/{usePlanet}", name="building_add_lightUsine", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddLightUsineAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getLightUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 6000) || $usePlanetWt < ($level * 3900)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getLightShip() == 0)) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2160) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 6000));
        $usePlanet->setWater($usePlanetWt - ($level * 3900));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('lightUsine');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-usine-legere/{usePlanet}", name="building_remove_lightUsine", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveLightUsineAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getLightUsine();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if(($level == 0 || $usePlanet->getConstructAt() > $now)
        || $usePlanet->getProduct()) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setLightUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.15);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/construire-usine-lourde/{usePlanet}", name="building_add_heavyUsine", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddHeavyUsineAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getHeavyUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 12;

        if(($usePlanetNb < ($level * 83000) || $usePlanetWt < ($level * 68000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getHeavyShip() == 0)) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 7200) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 83000));
        $usePlanet->setWater($usePlanetWt - ($level * 68000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('heavyUsine');
        $usePlanet->setConstructAt($now);
        $em->flush();



        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-usine-lourde/{usePlanet}", name="building_remove_heavyUsine", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveHeavyUsineAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getHeavyUsine();
        $newGround = $usePlanet->getGroundPlace() - 12;

        if(($level == 0 || $usePlanet->getConstructAt() > $now)
        || $usePlanet->getProduct()) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setHeavyUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.3);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/construire-caserne/{usePlanet}", name="building_add_caserne", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddCaserneAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCaserne() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 13000) || $usePlanetWt < ($level * 19000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDiscipline() == 0)) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2100) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 13000));
        $usePlanet->setWater($usePlanetWt - ($level * 19000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('caserne');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-caserne/{usePlanet}", name="building_remove_caserne", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveCaserneAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getCaserne();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getSoldier() > $usePlanet->getSoldierMax() - 2500 || $usePlanet->getSoldierAt())) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setCaserne($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSoldierMax($usePlanet->getSoldierMax() - 2500);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/construire-bunker/{usePlanet}", name="building_add_bunker", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddBunkerAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getBunker() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 10;

        if(($usePlanetNb < ($level * 200000) || $usePlanetWt < ($level * 190000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDiscipline() == 0)) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 4320) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 200000));
        $usePlanet->setWater($usePlanetWt - ($level * 190000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('bunker');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-bunker/{usePlanet}", name="building_remove_bunker", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveBunkerAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getBunker();
        $newGround = $usePlanet->getGroundPlace() - 10;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getSoldier() > $usePlanet->getSoldierMax() - 20000 || $usePlanet->getSoldierAt())) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setBunker($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSoldierMax($usePlanet->getSoldierMax() - 20000);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/construire-nucleaire/{usePlanet}", name="building_add_nuclear", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddNuclearAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getNuclearBase() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $newGround = $usePlanet->getGroundPlace() + 2;

        if(($usePlanetNb < ($level * 1000000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            $level == 6) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 4320) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 1000000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('nuclearBase');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-nucleaire/{usePlanet}", name="building_remove_nuclear", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveNuclearAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getNuclearBase();
        $newGround = $usePlanet->getGroundPlace() - 2;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            $usePlanet->getSoldier() > $usePlanet->getNuclearMax() - 1) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setNuclearBase($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }
}