<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;
use DateTimeZone;
use Dateinterval;

class InstantController extends Controller
{
    /**
     * @Route("/resources/", name="ressources_load")
     */
    public function minuteLoadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            foreach ($user->getPlanets() as $planet) {
                $niobium = $planet->getNiobium();
                $water = $planet->getWater();
                $niobium = $niobium + ($planet->getNbProduction());
                $water = $water + ($planet->getWtProduction());
                $planet->setNiobium($niobium);
                $planet->setWater($water);
                $em->persist($planet);
            }
            $em->persist($user);
        }
        $em->flush();

        exit;
    }

    /**
     * @Route("/construction/", name="build_fleet_load")
     */
    public function buildFleetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.searchAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.flightTime < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.constructAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        foreach ($planets as $planet) {
            $build = $planet->getConstruct();
            if($build == 'miner') {
                $planet->setMiner($planet->getMiner() + 1);
                $planet->setNbProduction($planet->getNbProduction() + ($planet->getMiner() * 1.1));
            } elseif ($build == 'extractor') {
                $planet->setExtractor($planet->getExtractor() + 1);
                $planet->setWtProduction($planet->getWtProduction() + ($planet->getExtractor() * 1.09));
            } elseif ($build == 'city') {
                $planet->setCity($planet->getCity() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 0.2);
            } elseif ($build == 'metropole') {
                $planet->setMetropole($planet->getMetropole() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 0.5);
            } elseif ($build == 'caserne') {
                $planet->setCaserne($planet->getCaserne() + 1);
                $planet->setSoldierMax($planet->getSoldierMax() + 2500);
            } elseif ($build == 'centerSearch') {
                $planet->setCenterSearch($planet->getCenterSearch() + 1);
                $planet->setScientistProduction($planet->getScientistProduction() + 0.1);
            } elseif ($build == 'lightUsine') {
                $planet->setLightUsine($planet->getLightUsine() + 1);
                $planet->setShipProduction($planet->getShipProduction() + 0.15);
            } elseif ($build == 'heavyUsine') {
                $planet->setHeavyUsine($planet->getHeavyUsine() + 1);
                $planet->setShipProduction($planet->getShipProduction() + 0.3);
            } elseif ($build == 'spaceShip') {
                $planet->setSpaceShip($planet->getSpaceShip() + 1);
                $planet->setShipProduction($planet->getShipProduction() + 0.1);
            } elseif ($build == 'radar') {
                $planet->setRadar($planet->getRadar() + 1);
            } elseif ($build == 'skyRadar') {
                $planet->setSkyRadar($planet->getSkyRadar() + 1);
            } elseif ($build == 'skyBrouilleur') {
                $planet->setSkyBrouilleur($planet->getSkyBrouilleur() + 1);
            }
            $planet->setConstruct(null);
            $planet->setConstructAt(null);
            $em->persist($planet);
        }

        foreach ($users as $user) {
            $research = $user->getSearch();
            if($research == 'onde') {
                $user->setOnde($user->getOnde() + 1);
            } elseif($research == 'industry') {
                $user->setIndustry($user->getIndustry() + 1);
            } elseif($research == 'discipline') {
                $user->setDiscipline($user->getDiscipline() + 1);
            } elseif($research == 'hyperespace') {
                $user->setHyperespace(1);
            } elseif($research == 'barge') {
                $user->setBarge(1);
            } elseif($research == 'utility') {
                $user->setUtility($user->getUtility() + 1);
            } elseif($research == 'demography') {
                $user->setDemography($user->getDemography() + 1);
            } elseif($research == 'terraformation') {
                $user->setTerraformation(1);
            } elseif($research == 'cargo') {
                $user->setCargo($user->getCargo() + 1);
            } elseif($research == 'recycleur') {
                $user->setRecycleur(1);
            } elseif($research == 'armement') {
                $user->setArmement($user->getArmement() + 1);
            } elseif($research == 'missile') {
                $user->setMissile($user->getMissile() + 1);
            } elseif($research == 'laser') {
                $user->setLaser($user->getLaser() + 1);
            } elseif($research == 'plasma') {
                $user->setPlasma($user->getPlasma() + 1);
            } elseif($research == 'lightShip') {
                $user->setLightShip($user->getLightShip() + 1);
            } elseif($research == 'heavyShip') {
                $user->setHeavyShip($user->getHeavyShip() + 1);
            }
            $user->setSearch(null);
            $user->setSearchAt(null);
            $em->persist($user);
        }

        foreach ($fleets as $fleet) {
            $allFleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.user', 'u')
                ->where('f.planet = :planet')
                ->andWhere('f.user != :user')
                ->setParameters(array('planet' => $fleet->getPlanet(), 'user' => $fleet->getUser()))
                ->getQuery()
                ->getResult();

            $eAlly = $fleet->getUser()->getAllyEnnemy();
            $warAlly = [];
            $x = 0;
            foreach ($eAlly as $tmp) {
                $warAlly[$x] = $tmp->getAllyTag();
                $x++;
            }

            $newHome = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.sector', 's')
                ->join('s.galaxy', 'g')
                ->where('p.position = :planete')
                ->andWhere('s.position = :sector')
                ->andWhere('g.position = :galaxy')
                ->setParameters(array('planete' => $fleet->getPlanete(), 'sector' => $fleet->getSector()->getPosition(), 'galaxy' => $fleet->getSector()->getGalaxy()->getPosition()))
                ->getQuery()
                ->getOneOrNullResult();

            $fleet->setPlanet($newHome);
            $fleet->setPlanete(null);
            $fleet->setFlightTime(null);
            $fleet->setSector(null);
            $attackFleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.user', 'u')
                ->leftJoin('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.attack = :true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->setParameters(array('planet' => $newHome, 'true' => true, 'ally' => $warAlly, 'user' => $fleet->getUser()))
                ->getQuery()
                ->getResult();

            if ($attackFleets) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));
                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                    $em->persist($updateF);
                }
                $fleet->setFightAt($now);
            }

            $em->persist($fleet);
        }
        $em->flush();
        exit;
    }
}
