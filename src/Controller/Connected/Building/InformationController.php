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
class InformationController extends Controller
{
    /**
     * @Route("/contruire-radar/{idp}", name="building_add_radar", requirements={"idp"="\d+"})
     */
    public function buildingAddRadarAction($idp)
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

        $radar = $usePlanet->getBuilding()->getRadar();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $radar->getGround();
        if(($usePlanetNb < $radar->getNiobium() || $usePlanetWt < $radar->getWater()) ||
            ($radar->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) ||
            ($newGround > $usePlanet->getGround() || $user->getResearch()->getOnde()->getLevel() == 0)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $radar->getLevel() / 5;
        if($radar->getLevel() % 3 == 0 && $radar->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $radar->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $radar->getNiobium());
        $usePlanet->setWater($usePlanetWt - $radar->getWater());
        $usePlanet->setGroundPlace($newGround);
        $radar->setNiobium($radar->getNiobium() * $cost);
        $radar->setWater($radar->getWater() * $cost);
        $radar->setLevel($radar->getLevel() + 1);
        $radar->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $radar->setConstructTime($radar->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($radar);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-radar/{idp}", name="building_remove_radar", requirements={"idp"="\d+"})
     */
    public function buildingRemoveRadarAction($idp)
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

        $radar = $usePlanet->getBuilding()->getRadar();
        $newGround = $usePlanet->getGroundPlace() - $radar->getGround();
        if($radar->getLevel() == 0 || $radar->getFinishAt() > $now ||
            ($usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $radar->getLevel() / 5;
        if($radar->getLevel() % 3 == 0 && $radar->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $radar->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $radar->setNiobium($radar->getNiobium() / $cost);
        $radar->setWater($radar->getWater() / $cost);
        $radar->setLevel($radar->getLevel() - 1);
        $radar->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $radar->setConstructTime($radar->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($radar->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($radar->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($radar);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-radar-espace/{idp}", name="building_add_skyRadar", requirements={"idp"="\d+"})
     */
    public function buildingSkyRadarAction($idp)
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

        $skyRadar = $usePlanet->getBuilding()->getSkyRadar();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + $skyRadar->getSky();
        if(($usePlanetNb < $skyRadar->getNiobium() || $usePlanetWt < $skyRadar->getWater()) ||
            ($skyRadar->getFinishAt() > $now || $newSky > $usePlanet->getSky()) ||
            ($user->getResearch()->getOnde()->getLevel() < 3 || $usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $skyRadar->getLevel() / 5;
        if($skyRadar->getLevel() % 3 == 0 && $skyRadar->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $skyRadar->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $skyRadar->getNiobium());
        $usePlanet->setWater($usePlanetWt - $skyRadar->getWater());
        $usePlanet->setSkyPlace($newSky);
        $skyRadar->setNiobium($skyRadar->getNiobium() * $cost);
        $skyRadar->setWater($skyRadar->getWater() * $cost);
        $skyRadar->setLevel($skyRadar->getLevel() + 1);
        $skyRadar->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $skyRadar->setConstructTime($skyRadar->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($skyRadar);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-radar-espace/{idp}", name="building_remove_skyRadar", requirements={"idp"="\d+"})
     */
    public function buildingRemoveSkyRadarAction($idp)
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

        $skyRadar = $usePlanet->getBuilding()->getSkyRadar();
        $newSky = $usePlanet->getSkyPlace() - $skyRadar->getSky();
        if($skyRadar->getLevel() == 0 || $skyRadar->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $skyRadar->getLevel() / 5;
        if($skyRadar->getLevel() % 3 == 0 && $skyRadar->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $skyRadar->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $skyRadar->setNiobium($skyRadar->getNiobium() / $cost);
        $skyRadar->setWater($skyRadar->getWater() / $cost);
        $skyRadar->setLevel($skyRadar->getLevel() - 1);
        $skyRadar->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $skyRadar->setConstructTime($skyRadar->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($skyRadar->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($skyRadar->getWater() / 1.5));
        $usePlanet->setSkyPlace($newSky);
        $em->persist($usePlanet);
        $em->persist($skyRadar);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-brouilleur/{idp}", name="building_add_brouilleur", requirements={"idp"="\d+"})
     */
    public function buildingBrouilleurAction($idp)
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

        $brouilleur = $usePlanet->getBuilding()->getSkyBrouilleur();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + $brouilleur->getSky();
        if(($usePlanetNb < $brouilleur->getNiobium() || $usePlanetWt < $brouilleur->getWater()) ||
            ($brouilleur->getFinishAt() > $now || $newSky > $usePlanet->getSky()) ||
            ($user->getResearch()->getOnde()->getLevel() > 5 || $usePlanet->getBuilding()->getConstruct() > $now)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $brouilleur->getLevel() / 5;
        if($brouilleur->getLevel() % 3 == 0 && $brouilleur->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $brouilleur->getConstructTime() . 'S'));
        $usePlanet->setNiobium($usePlanetNb - $brouilleur->getNiobium());
        $usePlanet->setWater($usePlanetWt - $brouilleur->getWater());
        $usePlanet->setSkyPlace($newSky);
        $brouilleur->setNiobium($brouilleur->getNiobium() * $cost);
        $brouilleur->setWater($brouilleur->getWater() * $cost);
        $brouilleur->setLevel($brouilleur->getLevel() + 1);
        $brouilleur->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $brouilleur->setConstructTime($brouilleur->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($brouilleur);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-brouilleur/{idp}", name="building_remove_brouilleur", requirements={"idp"="\d+"})
     */
    public function buildingRemoveBrouilleurAction($idp)
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

        $brouilleur = $usePlanet->getBuilding()->getSkyBrouilleur();
        $newSky = $usePlanet->getSkyPlace() - $brouilleur->getSky();
        if($brouilleur->getLevel() == 0 || $brouilleur->getFinishAt() > $now || $usePlanet->getBuilding()->getConstruct() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $cost = 1.7;
        $time = 1.5;
        $multiple = $brouilleur->getLevel() / 5;
        if($brouilleur->getLevel() % 3 == 0 && $brouilleur->getLevel() < 12) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.3 * $multiple);
        }
        $now->add(new DateInterval('PT' . $brouilleur->getConstructTime() . 'S'));
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $brouilleur->setNiobium($brouilleur->getNiobium() / $cost);
        $brouilleur->setWater($brouilleur->getWater() / $cost);
        $brouilleur->setLevel($brouilleur->getLevel() - 1);
        $brouilleur->setFinishAt($now);
        $usePlanet->getBuilding()->setConstruct($now);
        $brouilleur->setConstructTime($brouilleur->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($brouilleur->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($brouilleur->getWater() / 1.5));
        $usePlanet->setSkyPlace($newSky);
        $em->persist($usePlanet);
        $em->persist($brouilleur);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}