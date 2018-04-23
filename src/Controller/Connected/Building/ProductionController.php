<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class ProductionController extends Controller
{
    /**
     * @Route("/contruire-mine/{idp}", name="building_add_mine", requirements={"idp"="\d+"})
     */
    public function buildingAddMineAction($idp)
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

        $miner = $usePlanet->getBuilding()->getMiner();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $miner->getGround();
        if(($usePlanetNb < $miner->getNiobium() || $usePlanetWt < $miner->getWater()) || ($miner->getFinishAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1.3;
        $cost = 1.6;
        $time = 1.8;
        $multiple = $miner->getLevel() / 5;
        if($miner->getLevel() % 5 == 0 && $miner->getLevel() < 25) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.19 * $multiple);
            $prod = $prod - (0.075 * $multiple);
        }
        $usePlanet->setNiobium($usePlanetNb - $miner->getNiobium());
        $usePlanet->setWater($usePlanetWt - $miner->getWater());
        $usePlanet->setGroundPlace($newGround);
        $miner->setNiobium($miner->getNiobium() * $cost);
        $miner->setWater($miner->getWater() * $cost);
        $miner->setProduction($miner->getProduction() * $prod);
        $miner->setLevel($miner->getLevel() + 1);
        $miner->setFinishAt($now);
        $miner->setConstructTime($miner->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($miner);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-mine/{idp}", name="building_remove_mine", requirements={"idp"="\d+"})
     */
    public function buildingRemoveMineAction($idp)
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

        $miner = $usePlanet->getBuilding()->getMiner();
        $newGround = $usePlanet->getGroundPlace() - $miner->getGround();
        if($miner->getLevel() == 1 || $miner->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1.3;
        $cost = 1.6;
        $time = 1.8;
        $multiple = $miner->getLevel() / 5;
        if($miner->getLevel() % 5 == 0 && $miner->getLevel() < 25) {
        $cost = $cost - (0.25 * $multiple);
        $time = $time + (0.19 * $multiple);
        $prod = $prod + (0.075 * $multiple);
        }
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $miner->setNiobium($miner->getNiobium() / $cost);
        $miner->setWater($miner->getWater() / $cost);
        $miner->setProduction($miner->getProduction() / $prod);
        $miner->setLevel($miner->getLevel() - 1);
        $miner->setFinishAt($now);
        $miner->setConstructTime($miner->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($miner->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($miner->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($miner);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/contruire-puit/{idp}", name="building_add_extract", requirements={"idp"="\d+"})
     */
    public function buildingAddExtractAction($idp)
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

        $extract = $usePlanet->getBuilding()->getExtractor();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + $extract->getGround();
        if(($usePlanetNb < $extract->getNiobium() || $usePlanetWt < $extract->getWater()) || ($extract->getFinishAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1.3;
        $cost = 1.6;
        $time = 1.8;
        $multiple = $extract->getLevel() / 5;
        if($extract->getLevel() % 5 == 0 && $extract->getLevel() < 25) {
            $cost = $cost + (0.25 * $multiple);
            $time = $time - (0.19 * $multiple);
            $prod = $prod - (0.075 * $multiple);
        }
        $usePlanet->setNiobium($usePlanetNb - $extract->getNiobium());
        $usePlanet->setWater($usePlanetWt - $extract->getWater());
        $usePlanet->setGroundPlace($newGround);
        $extract->setNiobium($extract->getNiobium() * $cost);
        $extract->setWater($extract->getWater() * $cost);
        $extract->setProduction($extract->getProduction() * $prod);
        $extract->setLevel($extract->getLevel() + 1);
        $extract->setFinishAt($now);
        $extract->setConstructTime($extract->getConstructTime() * $time);
        $em->persist($usePlanet);
        $em->persist($extract);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-puit/{idp}", name="building_remove_extract", requirements={"idp"="\d+"})
     */
    public function buildingRemoveExtractAction($idp)
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

        $extract = $usePlanet->getBuilding()->getExtractor();
        $newGround = $usePlanet->getGroundPlace() - $extract->getGround();
        if($extract->getLevel() == 1 || $extract->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $prod = 1.3;
        $cost = 1.6;
        $time = 1.8;
        $multiple = $extract->getLevel() / 5;
        if($extract->getLevel() % 5 == 0 && $extract->getLevel() < 25) {
            $cost = $cost - (0.25 * $multiple);
            $time = $time + (0.19 * $multiple);
            $prod = $prod + (0.075 * $multiple);
        }
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $extract->setNiobium($extract->getNiobium() / $cost);
        $extract->setWater($extract->getWater() / $cost);
        $extract->setProduction($extract->getProduction() / $prod);
        $extract->setLevel($extract->getLevel() - 1);
        $extract->setFinishAt($now);
        $extract->setConstructTime($extract->getConstructTime() / $time);
        $usePlanet->setNiobium($usePlanetNb + ($extract->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($extract->getWater() / 1.5));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->persist($extract);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}