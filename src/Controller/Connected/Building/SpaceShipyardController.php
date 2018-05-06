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
     * @Route("/contruire-chantier-spatiale/{idp}", name="building_add_spaceShipyard", requirements={"idp"="\d+"})
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

        $level = $usePlanet->getSpaceShip() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 10;
        $newSky = $usePlanet->getSkyPlace() + 4;

        if(($usePlanetNb < ($level * 3000) || $usePlanetWt < ($level * 2000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getIndustry() == 0 || $newSky > $usePlanet->getSky())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 7000) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 3000));
        $usePlanet->setWater($usePlanetWt - ($level * 2000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('spaceShip');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-chantier-spatiale/{idp}", name="building_remove_spaceShipyard", requirements={"idp"="\d+"})
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

        $level = $usePlanet->getSpaceShip();
        $newGround = $usePlanet->getGroundPlace() - 10;
        $newSky = $usePlanet->getSkyPlace() - 4;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setSpaceShip($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.1);
        $usePlanet->setSkyPlace($newSky);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getLightUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 6000) || $usePlanetWt < ($level * 3900)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getLightShip() == 0)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 21600) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 6000));
        $usePlanet->setWater($usePlanetWt - ($level * 3900));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('lightUsine');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getLightUsine();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setLightUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.15);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getHeavyUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 12;

        if(($usePlanetNb < ($level * 83000) || $usePlanetWt < ($level * 68000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getHeavyShip() == 0)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 72000) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 83000));
        $usePlanet->setWater($usePlanetWt - ($level * 68000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('heavyUsine');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getHeavyUsine();
        $newGround = $usePlanet->getGroundPlace() - 12;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setHeavyUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.3);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getCaserne() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 23000) || $usePlanetWt < ($level * 34000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDiscipline() < 3)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 21000) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 23000));
        $usePlanet->setWater($usePlanetWt - ($level * 34000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('caserne');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
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

        $level = $usePlanet->getCaserne();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getSoldier() < $usePlanet->getSoldierMax() - 2500)) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setMiner($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSoldierMax($usePlanet->getSoldierMax() - 2500);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}