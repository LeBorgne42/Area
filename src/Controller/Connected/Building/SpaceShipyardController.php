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
class SpaceShipyardController extends Controller
{
    /**
     * @Route("/contruire-chantier-spatial/{idp}", name="building_add_spaceShipyard", requirements={"idp"="\d+"})
     */
    public function buildingAddSpaceShipyardAction($idp)
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

        $spaceShip = $usePlanet->getBuilding()->getSpaceShip();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $spaceShip->getGround();
        $newSky = $usePlanet->getSkyPlace() + $spaceShip->getSky();
        if(($usePlanetNb < $spaceShip->getNiobium() || $usePlanetWt < $spaceShip->getWater()) ||
            ($spaceShip->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($newSky > $usePlanet->getSky() || $user->getResearch()->getIndustry()->getLevel() == 0) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
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
        $now->add(new DateInterval('PT' . $spaceShip->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $spaceShip->getNiobium());
        $usePlanet->setWater($usePlanetWt - $spaceShip->getWater());
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $spaceShip->setNiobium($spaceShip->getNiobium() * $cost);
        $spaceShip->setWater($spaceShip->getWater() * $cost);
        $spaceShip->setProduction($spaceShip->getProduction() + $prod);
        $spaceShip->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
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
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
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
        $newSky = $usePlanet->getSkyPlace() - $spaceShip->getSky();
        if($spaceShip->getLevel() == 0 || $spaceShip->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
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
        $now->add(new DateInterval('PT' . $spaceShip->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $spaceShip->setNiobium($spaceShip->getNiobium() / $cost);
        $spaceShip->setWater($spaceShip->getWater() / $cost);
        $spaceShip->setProduction($spaceShip->getProduction() - $prod);
        $spaceShip->setLevel($spaceShip->getLevel() - 1);
        $spaceShip->setConstructTime($spaceShip->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($spaceShip->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($spaceShip->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $em->persist($usePlanet);
        $em->persist($spaceShip);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-usine-legere/{idp}", name="building_add_lightUsine", requirements={"idp"="\d+"})
     */
    public function buildingAddLightUsineAction($idp)
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

        $lightUsine = $usePlanet->getBuilding()->getLightUsine();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $lightUsine->getGround();
        if(($usePlanetNb < $lightUsine->getNiobium() || $usePlanetWt < $lightUsine->getWater()) ||
            ($lightUsine->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($lightUsine->getLevel() != 0 || $user->getResearch()->getLightShip()->getLevel() == 0) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . $lightUsine->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $lightUsine->getNiobium());
        $usePlanet->setWater($usePlanetWt - $lightUsine->getWater());
        $usePlanet->setGroundPlace($newGround);
        $lightUsine->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $em->persist($usePlanet);
        $em->persist($lightUsine);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-usine-legere/{idp}", name="building_remove_lightUsine", requirements={"idp"="\d+"})
     */
    public function buildingRemoveLightUsineAction($idp)
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

        $lightUsine = $usePlanet->getBuilding()->getLightUsine();
        $newGround = $usePlanet->getGroundPlace() - $lightUsine->getGround();
        if($lightUsine->getLevel() == 0 || $lightUsine->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . $lightUsine->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $lightUsine->setLevel(0);
        $usePlanet->setNiobium($usePlanetNb + ($lightUsine->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($lightUsine->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($lightUsine);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-usine-lourde/{idp}", name="building_add_heavyUsine", requirements={"idp"="\d+"})
     */
    public function buildingAddHeavyUsineAction($idp)
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

        $heavyUsine = $usePlanet->getBuilding()->getHeavyUsine();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $heavyUsine->getGround();
        if(($usePlanetNb < $heavyUsine->getNiobium() || $usePlanetWt < $heavyUsine->getWater()) ||
            ($heavyUsine->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($heavyUsine->getLevel() != 0 || $user->getResearch()->getHeavyShip()->getLevel() == 0) ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . $heavyUsine->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $heavyUsine->getNiobium());
        $usePlanet->setWater($usePlanetWt - $heavyUsine->getWater());
        $usePlanet->setGroundPlace($newGround);
        $heavyUsine->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $em->persist($usePlanet);
        $em->persist($heavyUsine);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-usine-lourde/{idp}", name="building_remove_heavyUsine", requirements={"idp"="\d+"})
     */
    public function buildingRemoveHeavyUsineAction($idp)
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

        $heavyUsine = $usePlanet->getBuilding()->getHeavyUsine();
        $newGround = $usePlanet->getGroundPlace() - $heavyUsine->getGround();
        if($heavyUsine->getLevel() == false || $heavyUsine->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . $heavyUsine->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $heavyUsine->setLevel(0);
        $usePlanet->setNiobium($usePlanetNb + ($heavyUsine->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($heavyUsine->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($heavyUsine);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-caserne/{idp}", name="building_add_caserne", requirements={"idp"="\d+"})
     */
    public function buildingAddCaserneAction($idp)
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

        $caserne = $usePlanet->getBuilding()->getCaserne();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $caserne->getGround();
        if(($usePlanetNb < $caserne->getNiobium() || $usePlanetWt < $caserne->getWater()) ||
            ($caserne->getFinishAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getResearch()->getDiscipline()->getLevel() < 3 || $usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.1;
        $cost = 2;
        $time = 3;
        $multiple = $caserne->getLevel() / 5;
        if($caserne->getLevel() % 3 == 0 && $caserne->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
            $prod = $prod - (0.02 * $multiple);
        }
        $now->add(new DateInterval('PT' . $caserne->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $caserne->getNiobium());
        $usePlanet->setWater($usePlanetWt - $caserne->getWater());
        $usePlanet->setGroundPlace($newGround);
        $caserne->setNiobium($caserne->getNiobium() * $cost);
        $caserne->setWater($caserne->getWater() * $cost);
        $caserne->setProduction($caserne->getProduction() + $prod);
        $caserne->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $caserne->setConstructTime($caserne->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($caserne);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-caserne/{idp}", name="building_remove_caserne", requirements={"idp"="\d+"})
     */
    public function buildingRemoveCaserneAction($idp)
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

        $caserne = $usePlanet->getBuilding()->getCaserne();
        $newGround = $usePlanet->getGroundPlace() - $caserne->getGround();
        if($caserne->getLevel() == 0 || $caserne->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 0.1;
        $cost = 2;
        $time = 3;
        $multiple = $caserne->getLevel() / 5;
        if($caserne->getLevel() % 3 == 0 && $caserne->getLevel() < 12) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.3 * $multiple);
            $prod = $prod + (0.02 * $multiple);
        }
        $now->add(new DateInterval('PT' . $caserne->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $caserne->setNiobium($caserne->getNiobium() / $cost);
        $caserne->setWater($caserne->getWater() / $cost);
        $caserne->setProduction($caserne->getProduction() - $prod);
        $caserne->setLevel($caserne->getLevel() - 1);
        $caserne->setConstructTime($caserne->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($caserne->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($caserne->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($caserne);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}