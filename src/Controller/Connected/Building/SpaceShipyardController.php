<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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
     * @Route("/contruire-chantier-spatiale/{idp}", name="building_add_spaceShipyard", requirements={"idp"="\d+"})
     */
    public function buildingAddSpaceShipyardAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getSpaceShip() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 2;
        $newSky = $usePlanet->getSkyPlace() + 1;

        if(($usePlanetNb < ($level * 3000) || $usePlanetWt < ($level * 2000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getIndustry() == 0 || $newSky > $usePlanet->getSky())) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 3000));
        $usePlanet->setWater($usePlanetWt - ($level * 2000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('spaceShip');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getSpaceShip();
        $newGround = $usePlanet->getGroundPlace() - 2;
        $newSky = $usePlanet->getSkyPlace() - 1;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setSpaceShip($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getLightUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 6000) || $usePlanetWt < ($level * 3900)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getLightShip() == 0)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2160) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 6000));
        $usePlanet->setWater($usePlanetWt - ($level * 3900));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('lightUsine');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getLightUsine();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setLightUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.15);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getHeavyUsine() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 12;

        if(($usePlanetNb < ($level * 83000) || $usePlanetWt < ($level * 68000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getHeavyShip() == 0)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 7200) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 83000));
        $usePlanet->setWater($usePlanetWt - ($level * 68000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('heavyUsine');
        $usePlanet->setConstructAt($now);
        $em->flush();



        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getHeavyUsine();
        $newGround = $usePlanet->getGroundPlace() - 12;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setHeavyUsine($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setShipProduction($usePlanet->getShipProduction() - 0.3);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getCaserne() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 6;

        if(($usePlanetNb < ($level * 13000) || $usePlanetWt < ($level * 19000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDiscipline() == 0)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2100) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 13000));
        $usePlanet->setWater($usePlanetWt - ($level * 19000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('caserne');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getCaserne();
        $newGround = $usePlanet->getGroundPlace() - 6;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getSoldier() > $usePlanet->getSoldierMax() - 2500)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setCaserne($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSoldierMax($usePlanet->getSoldierMax() - 2500);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-bunker/{idp}", name="building_add_bunker", requirements={"idp"="\d+"})
     */
    public function buildingAddBunkerAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getBunker() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 10;

        if(($usePlanetNb < ($level * 200000) || $usePlanetWt < ($level * 190000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            ($user->getDiscipline() == 0)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 4320) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 200000));
        $usePlanet->setWater($usePlanetWt - ($level * 190000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('bunker');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-bunker/{idp}", name="building_remove_bunker", requirements={"idp"="\d+"})
     */
    public function buildingRemoveBunkerAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getBunker();
        $newGround = $usePlanet->getGroundPlace() - 10;

        if(($level == 0 || $usePlanet->getConstructAt() > $now) ||
            ($usePlanet->getSoldier() > $usePlanet->getSoldierMax() - 20000)) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setBunker($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setSoldierMax($usePlanet->getSoldierMax() - 20000);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }
}