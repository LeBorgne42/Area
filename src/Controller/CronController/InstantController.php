<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Report;
use App\Entity\Fleet;
use App\Entity\Rank;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Dateinterval;

class InstantController extends AbstractController
{
    /**
     * @Route("/construction/", name="build_fleet_load")
     */
    public function buildFleetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $nowAste = new DateTime();
        $nowAste->setTimezone(new DateTimeZone('Europe/Paris'));

        $asteroides = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.cdr = true')
            ->andWhere('p.recycleAt < :nowAste OR p.recycleAt IS NULL')
            ->setParameters(['nowAste' => $nowAste])
            ->getQuery()
            ->getResult();

        if($asteroides) {
            foreach ($asteroides as $asteroide) {

                $nowAste->add(new DateInterval('PT' . 57600 . 'S'));
                $asteroide->setRecycleAt($nowAste);
                $asteroide->setNbCdr($asteroide->getNbCdr() + rand(1500000, 8000000));
                $asteroide->setWtCdr($asteroide->getWtCdr() + rand(1200000, 7800000));


                if(rand(1, 50) == 50) {
                    $asteroide->setCdr(false);
                    $asteroide->setEmpty(true);
                    $asteroide->setImageName(null);
                    $asteroide->setRecycleAt(null);
                    $asteroide->setName('Vide');
                    $newAsteroides = $em->getRepository('App:Planet')
                        ->createQueryBuilder('p')
                        ->join('p.sector', 's')
                        ->join('s.galaxy', 'g')
                        ->where('p.empty = true')
                        ->andWhere('s.position = :rand')
                        ->andWhere('g.position = :galaxy')
                        ->setParameters(['rand' => rand(1, 100), 'galaxy' => $asteroide->getSector()->getGalaxy()->getPosition()])
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    if ($newAsteroides) {
                        $newAsteroides->setEmpty(false);
                        $newAsteroides->setCdr(true);
                        $newAsteroides->setImageName('cdr.png');
                        $newAsteroides->setName('Astéroïdes');
                        $iaPlayer = $em->getRepository('App:User')->findOneBy(['zombie' => 1]);
                        $fleet = new Fleet();
                        $fleet->setHunterWar(rand(50, 100000));
                        $fleet->setCorvetWar(rand(50, 20000));
                        $fleet->setFregatePlasma(rand(20, 1000));
                        $fleet->setDestroyer(rand(1, 5000));
                        $fleet->setUser($iaPlayer);
                        $fleet->setPlanet($newAsteroides);
                        $fleet->setAttack(1);
                        $fleet->setName('Horde Zombie');
                        $em->persist($fleet);
                    }
                }
            }
        }

        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 1 . 'S'));

        $planetSoldiers = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.soldierAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $planetTanks = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.tankAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $planetScientists = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.scientistAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.flightTime < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $fleetCdrs = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.recycleAt < :now or f.recycleAt is null')
            ->andWhere('f.recycleur > :zero')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.nbCdr > :zero or p.wtCdr > :zero')
            ->setParameters(['now' => $now, 'zero' => 0])
            ->getQuery()
            ->getResult();

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.constructAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $products = $em->getRepository('App:Product')
            ->createQueryBuilder('p')
            ->where('p.productAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $radars = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.radarAt < :now or p.brouilleurAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $pacts = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.dismissAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $peaces = $em->getRepository('App:Peace')
            ->createQueryBuilder('p')
            ->where('p.signedAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        $now->sub(new DateInterval('PT' . 1 . 'S'));

        foreach ($peaces as $peace) {
            $em->remove($peace);
        }

        foreach ($pacts as $pact) {
            $otherAlly = $em->getRepository('App:Ally')
                ->createQueryBuilder('a')
                ->where('a.sigle = :sigle')
                ->setParameter('sigle', $pact->getAllyTag())
                ->getQuery()
                ->getOneOrNullResult();

            $pact2 = $em->getRepository('App:Allied')
                ->createQueryBuilder('al')
                ->where('al.allyTag = :allytag')
                ->andWhere('al.ally = :ally')
                ->setParameters([
                    'allytag' => $pact->getDismissBy(),
                    'ally' => $otherAlly])
                ->getQuery()
                ->getOneOrNullResult();

            $salons = $em->getRepository('App:Salon')
                ->createQueryBuilder('s')
                ->where('s.name = :sigle1')
                ->orWhere('s.name = :sigle2')
                ->setParameters(['sigle1' => $otherAlly->getSigle() . " - " . $pact->getDismissBy(), 'sigle2' => $pact->getDismissBy() . " - " . $otherAlly->getSigle()])
                ->getQuery()
                ->getResult();

            foreach($salons as $salon) {
                foreach($salon->getContents() as $content) {
                    $em->remove($content);
                }
                $em->remove($salon);
            }

            if($pact2) {
                $em->remove($pact2);
            }
            $em->remove($pact);
        }

        foreach ($planetSoldiers as $soldierAt) {
            $soldierAt->setSoldier($soldierAt->getSoldier() + $soldierAt->getSoldierAtNbr());
            $soldierAt->setSoldierAt(null);
            $soldierAt->setSoldierAtNbr(null);
        }

        foreach ($planetTanks as $tankAt) {
            $tankAt->setTank($tankAt->getTank() + $tankAt->getTankAtNbr());
            $tankAt->setTankAt(null);
            $tankAt->setTankAtNbr(null);
        }

        foreach ($planetScientists as $scientistAt) {
            $scientistAt->setScientist($scientistAt->getScientist() + $scientistAt->GetScientistAtNbr());
            $scientistAt->getUser()->setScientistProduction(round($scientistAt->getUser()->getScientistProduction() + ($scientistAt->getScientist() / 10000)));
            $scientistAt->setScientistAt(null);
            $scientistAt->setScientistAtNbr(null);
        }

        foreach ($radars as $radar) {
            if($radar->getRadarAt() < $now && $radar->getMoon() == false) {
                if(!$radar->getRadarAt()) {
                    $radar->setUser(null);
                }
                $radar->setName('Vide');
                $radar->setSkyRadar(0);
                $radar->setRadarAt(null);
            }
            if($radar->getBrouilleurAt() < $now && $radar->getMoon() == false) {
                if(!$radar->getBrouilleurAt()) {
                    $radar->setUser(null);
                }
                $radar->setName('Vide');
                $radar->setSkyBrouilleur(0);
                $radar->setBrouilleurAt(null);
            }
            if($radar->getMoon() == true) {
                $radar->setSkyBrouilleur(0);
                $radar->setBrouilleurAt(null);
                $radar->setSkyRadar(0);
                $radar->setRadarAt(null);
            }
        }

        $tmpNoCdr = new DateTime();
        $tmpNoCdr->setTimezone(new DateTimeZone('Europe/Paris'));
        $tmpNoCdr->add(new DateInterval('PT' . 600 . 'S'));
        foreach ($fleetCdrs as $fleetCdr) {
            if ($fleetCdr->getUser()->getPoliticRecycleur() > 0) {
                $recycle = $fleetCdr->getRecycleur() * (1000 + ($fleetCdr->getUser()->getPoliticRecycleur() * 200));
            } else {
                $recycle = $fleetCdr->getRecycleur() * 1000;
            }
            $planetCdr = $fleetCdr->getPlanet();
            if ($fleetCdr->getCargoPlace() > ($fleetCdr->getCargoFull() + ($recycle * 2))) {
                if($planetCdr->getNbCdr() == 0 || $planetCdr->getWtCdr() == 0) {
                    $recycle = $recycle * 2;
                }
                if ($planetCdr->getNbCdr() > $recycle) {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $recycle);
                    $planetCdr->setNbCdr($planetCdr->getNbCdr() - $recycle);
                } else {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $planetCdr->getNbCdr());
                    $planetCdr->setNbCdr(0);
                }
                if ($planetCdr->getWtCdr() > $recycle) {
                    $fleetCdr->setWater($fleetCdr->getWater() + $recycle);
                    $planetCdr->setWtCdr($planetCdr->getWtCdr() - $recycle);
                } else {
                    $fleetCdr->setWater($fleetCdr->getWater() + $planetCdr->getWtCdr());
                    $planetCdr->setWtCdr(0);
                }
                if($planetCdr->getNbCdr() == 0 && $planetCdr->getWtCdr() == 0) {
                    $fleetCdr->setRecycleAt(null);
                    if ($fleetCdr->getCargoPlace() > ($fleetCdr->getCargoFull() + ($recycle * 2))) {
                        $reportRec = new Report();
                        $reportRec->setTitle("Votre flotte " . $fleetCdr->getName() . " a arrêté de recycler!");
                        $reportRec->setImageName("recycle_report.jpg");
                        $reportRec->setSendAt($now);
                        $reportRec->setUser($fleetCdr->getUser());
                        $usePlanet = $em->getRepository('App:Planet')->findByFirstPlanet($fleetCdr->getUser()->getUsername());
                        $reportRec->setContent("Bonjour dirigeant " . $fleetCdr->getUser()->getUserName() . " votre flotte " . "<span><a href='/connect/gerer-flotte/" . $usePlanet->getId() ."/" . $fleetCdr->getId() . "' data-toggle='modal' data-target='#editModal'>" . $fleetCdr->getName() . "</a></span>" . " vient de terminer de recycler en " . "<span><a href='/connect/carte-spatiale/" . $fleetCdr->getPlanet()->getSector()->getPosition() ."/" . $fleetCdr->getPlanet()->getSector()->getGalaxy()->getPosition() ."/" . $usePlanet->getId() . "'>" . $fleetCdr->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $fleetCdr->getPlanet()->getSector()->getPosition() . ":" . $fleetCdr->getPlanet()->getPosition() . "</a></span>.");
                        $em->persist($reportRec);
                        $fleetCdr->getUser()->setViewReport(false);
                    }
                } else {
                    $fleetCdr->setRecycleAt($tmpNoCdr);
                }
            } else {
                if($planetCdr->getNbCdr() == 0 || $planetCdr->getWtCdr() == 0) {
                    $recycle = ($fleetCdr->getCargoPlace() >= ($fleetCdr->getCargoFull() + ($recycle * 2))) ? $recycle * 2 : (($fleetCdr->getCargoPlace() - $fleetCdr->getCargoFull()));
                } else {
                    $recycle = ($fleetCdr->getCargoPlace() >= ($fleetCdr->getCargoFull() + ($recycle * 2))) ? $recycle : (($fleetCdr->getCargoPlace() - $fleetCdr->getCargoFull()) / 2);
                }
                if ($planetCdr->getNbCdr() > $recycle) {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $recycle);
                    $planetCdr->setNbCdr($planetCdr->getNbCdr() - $recycle);
                } else {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $planetCdr->getNbCdr());
                    $planetCdr->setNbCdr(0);
                }
                if ($planetCdr->getWtCdr() > $recycle) {
                    $fleetCdr->setWater($fleetCdr->getWater() + $recycle);
                    $planetCdr->setWtCdr($planetCdr->getWtCdr() - $recycle);
                } else {
                    $fleetCdr->setWater($fleetCdr->getWater() + $planetCdr->getWtCdr());
                    $planetCdr->setWtCdr(0);
                }
                if ($planetCdr->getNbCdr() == 0 && $planetCdr->getWtCdr() == 0 || $fleetCdr->getCargoPlace() == $fleetCdr->getCargoFull()) {
                    $fleetCdr->setRecycleAt(null);
                } else {
                    $fleetCdr->setRecycleAt($tmpNoCdr);
                }
            }
            $quest = $fleetCdr->getUser()->checkQuests('recycle');
            if($quest) {
                $fleetCdr->getUser()->getRank()->setWarPoint($fleetCdr->getUser()->getRank()->getWarPoint() + $quest->getGain());
                $fleetCdr->getUser()->removeQuest($quest);
            }
        }

        foreach ($planets as $planet) {
            $build = $planet->getConstruct();
            if($build == 'destruct') {
            } elseif ($build == 'miner') {
                $planet->setMiner($planet->getMiner() + 1);
                $planet->setNbProduction($planet->getNbProduction() + ($planet->getMiner() * 1.1));
            } elseif ($build == 'extractor') {
                $planet->setExtractor($planet->getExtractor() + 1);
                $planet->setWtProduction($planet->getWtProduction() + ($planet->getExtractor() * 1.09));
            } elseif ($build == 'niobiumStock') {
                $planet->setNiobiumStock($planet->getNiobiumStock() + 1);
                $planet->setNiobiumMax($planet->getNiobiumMax() + 1000000);
            } elseif ($build == 'waterStock') {
                $planet->setWaterStock($planet->getWaterStock() + 1);
                $planet->setWaterMax($planet->getWaterMax() + 1000000);
            } elseif ($build == 'city') {
                $planet->setCity($planet->getCity() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 2000);
                $planet->setWorkerMax($planet->getWorkerMax() + 25000);
                $quest = $planet->getUser()->checkQuests('build_city');
                if($quest) {
                    $planet->getUser()->getRank()->setWarPoint($planet->getUser()->getRank()->getWarPoint() + $quest->getGain());
                    $planet->getUser()->removeQuest($quest);
                }
            } elseif ($build == 'metropole') {
                $planet->setMetropole($planet->getMetropole() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 5000);
                $planet->setWorkerMax($planet->getWorkerMax() + 75000);
                $quest = $planet->getUser()->checkQuests('build_metro');
                if($quest) {
                    $planet->getUser()->getRank()->setWarPoint($planet->getUser()->getRank()->getWarPoint() + $quest->getGain());
                    $planet->getUser()->removeQuest($quest);
                }
            } elseif ($build == 'caserne') {
                $planet->setCaserne($planet->getCaserne() + 1);
                $planet->setSoldierMax($planet->getSoldierMax() + 2500);
            } elseif ($build == 'bunker') {
                $planet->setBunker($planet->getBunker() + 1);
                $planet->setSoldierMax($planet->getSoldierMax() + 20000);
            } elseif ($build == 'island') {
                $planet->setIsland($planet->getIsland() + 1);
                $planet->setGround($planet->getGround() + 10);
            } elseif ($build == 'orbital') {
                $planet->setOrbital($planet->getOrbital() + 1);
                $planet->setSky($planet->getSky() + 5);
            } elseif ($build == 'centerSearch') {
                $planet->setCenterSearch($planet->getCenterSearch() + 1);
                $planet->setScientistMax($planet->getScientistMax() + 500);
            } elseif ($build == 'lightUsine') {
                $planet->setLightUsine($planet->getLightUsine() + 1);
                $planet->setShipProduction($planet->getShipProduction() + 0.15);
            } elseif ($build == 'heavyUsine') {
                $planet->setHeavyUsine($planet->getHeavyUsine() + 1);
                $planet->setShipProduction($planet->getShipProduction() + 0.3);
                $quest = $planet->getUser()->checkQuests('build_heavy');
                if($quest) {
                    $planet->getUser()->getRank()->setWarPoint($planet->getUser()->getRank()->getWarPoint() + $quest->getGain());
                    $planet->getUser()->removeQuest($quest);
                }
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
            if(count($planet->getConstructions()) > 0) {
                $constructTime = new DateTime();
                $constructTime->setTimezone(new DateTimeZone('Europe/Paris'));
                foreach ($planet->getConstructions() as $construction) {
                    $planet->setConstruct($construction->getConstruct());
                    $planet->setConstructAt($constructTime->add(new DateInterval('PT' . $construction->getConstructTime() . 'S')));
                    $em->remove($construction);
                    break;
                }
            } else {
                $planet->setConstruct(null);
                $planet->setConstructAt(null);
            }
            $server->setNbrBuilding($server->getNbrBuilding() + 1);
        }

        foreach ($products as $product) {
            $planetProduct = $product->getPlanet();
            $planetProduct->setCargoI($planetProduct->getCargoI() + $product->getCargoI());
            $planetProduct->setCargoV($planetProduct->getCargoV() + $product->getCargoV());
            $planetProduct->setCargoX($planetProduct->getCargoX() + $product->getCargoX());
            $planetProduct->setColonizer($planetProduct->getColonizer() + $product->getColonizer());
            $planetProduct->setRecycleur($planetProduct->getRecycleur() + $product->getRecycleur());
            $planetProduct->setBarge($planetProduct->getBarge() + $product->getBarge());
            $planetProduct->setMoonMaker($planetProduct->getMoonMaker() + $product->getMoonMaker());
            $planetProduct->setRadarShip($planetProduct->getRadarShip() + $product->getRadarShip());
            $planetProduct->setBrouilleurShip($planetProduct->getBrouilleurShip() + $product->getBrouilleurShip());
            $planetProduct->setMotherShip($planetProduct->getMotherShip() + $product->getMotherShip());
            $planetProduct->setSonde($planetProduct->getSonde() + $product->getSonde());
            $planetProduct->setHunter($planetProduct->getHunter() + $product->getHunter());
            $planetProduct->setFregate($planetProduct->getFregate() + $product->getFregate());
            $planetProduct->setHunterHeavy($planetProduct->getHunterHeavy() + $product->getHunterHeavy());
            $planetProduct->setHunterWar($planetProduct->getHunterWar() + $product->getHunterWar());
            $planetProduct->setCorvet($planetProduct->getCorvet() + $product->getCorvet());
            $planetProduct->setCorvetLaser($planetProduct->getCorvetLaser() + $product->getCorvetLaser());
            $planetProduct->setCorvetWar($planetProduct->getCorvetWar() + $product->getCorvetWar());
            $planetProduct->setFregatePlasma($planetProduct->getFregatePlasma() + $product->getFregatePlasma());
            $planetProduct->setCroiser($planetProduct->getCroiser() + $product->getCroiser());
            $planetProduct->setIronClad($planetProduct->getIronClad() + $product->getIronClad());
            $planetProduct->setDestroyer($planetProduct->getDestroyer() + $product->getDestroyer());
            $em->remove($product);
        }

        $nowReport = new DateTime();
        $nowReport->setTimezone(new DateTimeZone('Europe/Paris'));
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        foreach ($fleets as $fleet) {
            if ($fleet->getName() == 'Cargos Marchands' && $fleet->getUser()->getId() == 1) {
                $em->remove($fleet);
            } else {
                $usePlanet = $em->getRepository('App:Planet')->findByFirstPlanet($fleet->getUser()->getUsername());
                if (!$usePlanet) {
                    $em->remove($fleet);
                } else {
                    $newHome = $em->getRepository('App:Planet')
                        ->createQueryBuilder('p')
                        ->join('p.sector', 's')
                        ->join('s.galaxy', 'g')
                        ->where('p.position = :planete')
                        ->andWhere('s.position = :sector')
                        ->andWhere('g.position = :galaxy')
                        ->setParameters(['planete' => $fleet->getPlanete(), 'sector' => $fleet->getSector()->getPosition(), 'galaxy' => $fleet->getSector()->getGalaxy()->getPosition()])
                        ->getQuery()
                        ->getOneOrNullResult();

                    $userFleet = $fleet->getUser();
                    $report = new Report();
                    $report->setTitle("Votre flotte " . $fleet->getName() . " est arrivée");
                    $report->setImageName("travel_report.jpg");
                    $report->setSendAt($now);
                    $report->setUser($userFleet);
                    $report->setContent("Bonjour dirigeant " . $userFleet->getUserName() . " votre flotte " . "<span><a href='/connect/gerer-flotte/" . $usePlanet->getId() . "/" . $fleet->getId() . "' data-toggle='modal' data-target='#editModal'>" . $fleet->getName() . "</a></span>" . " vient d'arriver en " . "<span><a href='/connect/carte-spatiale/" . $newHome->getSector()->getPosition() . "/" . $newHome->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $newHome->getSector()->getGalaxy()->getPosition() . ":" . $newHome->getSector()->getPosition() . ":" . $newHome->getPosition() . "</a></span>.");
                    $userFleet->setViewReport(false);
                    $oldPlanet = $fleet->getPlanet();
                    $fleet->setPlanet($newHome);
                    $fleet->setPlanete(null);
                    $fleet->setFlightTime(null);
                    $fleet->setNewPlanet(null);
                    $fleet->setSector(null);

                    $user = $fleet->getUser();
                    $eAlly = $user->getAllyEnnemy();
                    $warAlly = [];
                    $x = 0;
                    foreach ($eAlly as $tmp) {
                        $warAlly[$x] = $tmp->getAllyTag();
                        $x++;
                    }

                    $fAlly = $user->getAllyFriends();
                    $friendAlly = [];
                    $x = 0;
                    foreach ($fAlly as $tmp) {
                        if ($tmp->getAccepted() == 1) {
                            $friendAlly[$x] = $tmp->getAllyTag();
                            $x++;
                        }
                    }
                    if (!$friendAlly) {
                        $friendAlly = ['impossible', 'personne'];
                    }

                    if ($user->getAlly()) {
                        $allyF = $user->getAlly();
                    } else {
                        $allyF = 'war';
                    }

                    $warFleets = $em->getRepository('App:Fleet')
                        ->createQueryBuilder('f')
                        ->join('f.user', 'u')
                        ->leftJoin('u.ally', 'a')
                        ->where('f.planet = :planet')
                        ->andWhere('f.attack = true OR a.sigle in (:ally)')
                        ->andWhere('f.user != :user')
                        ->andWhere('f.flightTime is null')
                        ->andWhere('u.ally is null OR a.sigle not in (:friend)')
                        ->andWhere('u.ally is null OR u.ally != :myAlly')
                        ->setParameters(['planet' => $newHome, 'ally' => $warAlly, 'user' => $user, 'friend' => $friendAlly, 'myAlly' => $allyF])
                        ->getQuery()
                        ->getResult();

                    $neutralFleets = $em->getRepository('App:Fleet')
                        ->createQueryBuilder('f')
                        ->join('f.user', 'u')
                        ->leftJoin('u.ally', 'a')
                        ->where('f.planet = :planet')
                        ->andWhere('f.user != :user')
                        ->andWhere('f.flightTime is null')
                        ->andWhere('u.ally is null OR a.sigle not in (:friend)')
                        ->setParameters(['planet' => $newHome, 'user' => $user, 'friend' => $friendAlly])
                        ->getQuery()
                        ->getResult();

                    $fleetFight = $em->getRepository('App:Fleet')
                        ->createQueryBuilder('f')
                        ->join('f.user', 'u')
                        ->leftJoin('u.ally', 'a')
                        ->where('f.planet = :planet')
                        ->andWhere('f.fightAt is not null')
                        ->andWhere('f.flightTime is null')
                        ->setParameters(['planet' => $newHome])
                        ->getQuery()
                        ->setMaxResults(1)
                        ->getOneOrNullResult();

                    if ($fleetFight) {
                        $fleet->setFightAt($fleetFight->getFightAt());
                    } elseif ($warFleets) {
                        foreach ($warFleets as $setWar) {
                            if ($setWar->getUser()->getAlly()) {
                                $fleetArm = $fleet->getMissile() + $fleet->getLaser() + $fleet->getPlasma();
                                if ($fleetArm > 0) {
                                    $fleet->setAttack(1);
                                }
                                foreach ($eAlly as $tmp) {
                                    if ($setWar->getUser()->getAlly()->getSigle() == $tmp->getAllyTag()) {
                                        $fleetArm = $setWar->getMissile() + $setWar->getLaser() + $setWar->getPlasma();
                                        if ($fleetArm > 0) {
                                            $setWar->setAttack(1);
                                        }
                                    }
                                }
                            }
                        }
                        $allFleets = $em->getRepository('App:Fleet')
                            ->createQueryBuilder('f')
                            ->join('f.user', 'u')
                            ->where('f.planet = :planet')
                            ->andWhere('f.flightTime is null')
                            ->setParameters(['planet' => $newHome])
                            ->getQuery()
                            ->getResult();

                        $nowWar = new DateTime();
                        $nowWar->setTimezone(new DateTimeZone('Europe/Paris'));
                        $nowWar->add(new DateInterval('PT' . 300 . 'S'));

                        foreach ($allFleets as $updateF) {
                            $updateF->setFightAt($nowWar);
                        }
                        $fleet->setFightAt($nowWar);
                        $report->setContent($report->getContent() . " Attention votre flotte est rentrée en combat !");
                        $report->setImageName("war_report.jpg");
                    } elseif ($neutralFleets && $fleet->getAttack() == 1) {
                        $allFleets = $em->getRepository('App:Fleet')
                            ->createQueryBuilder('f')
                            ->join('f.user', 'u')
                            ->where('f.planet = :planet')
                            ->andWhere('f.flightTime is null')
                            ->setParameters(['planet' => $newHome])
                            ->getQuery()
                            ->getResult();

                        $nowWar = new DateTime();
                        $nowWar->setTimezone(new DateTimeZone('Europe/Paris'));
                        $nowWar->add(new DateInterval('PT' . 300 . 'S'));

                        foreach ($allFleets as $updateF) {
                            $updateF->setFightAt($nowWar);
                        }
                        $fleet->setFightAt($nowWar);
                        $report->setContent($report->getContent() . " Votre flotte vient d''engager le combat !");
                        $report->setImageName("war_report.jpg");
                    }
                    if ($fleet->getFightAt() == null) {
                        $user = $fleet->getUser();
                        $newPlanet = $fleet->getPlanet();

                        if ($fleet->getFlightType() == '1') {
                            $em->persist($report);
                        }
                        if ($fleet->getFlightType() == '2') {
                            if ($newPlanet->getMerchant() == true) {
                                $reportSell = new Report();
                                $reportSell->setSendAt($nowReport);
                                $reportSell->setUser($user);
                                $reportSell->setTitle("Vente aux marchands");
                                $reportSell->setImageName("sell_report.jpg");
                                $newWarPointS = round((($fleet->getScientist() * 100) + ($fleet->getWorker() * 50) + ($fleet->getSoldier() * 10) + ($fleet->getWater() / 3) + ($fleet->getNiobium() / 6)) / 1000);
                                if ($fleet->getUser()->getAlly() && $fleet->getUser()->getAlly()->getPolitic() == 'democrat') {
                                    if ($user->getPoliticMerchant() > 0) {
                                        $gainSell = (($fleet->getWater() * 0.5) + ($fleet->getSoldier() * 5) + ($fleet->getWorker() * 2) + ($fleet->getScientist() * 50) + ($fleet->getNiobium() * 0.25)) * (1 + ($fleet->getUser()->getPoliticMerchant() / 20));
                                    } else {
                                        $gainSell = ($fleet->getWater() * 0.5) + ($fleet->getSoldier() * 5) + ($fleet->getWorker() * 2) + ($fleet->getScientist() * 50) + ($fleet->getNiobium() * 0.25);
                                    }
                                } else {
                                    $gainSell = ($fleet->getWater() * 0.5) + ($fleet->getSoldier() * 5) + ($fleet->getWorker() * 2) + ($fleet->getScientist() * 50) + ($fleet->getNiobium() * 0.25);
                                }
                                $reportSell->setContent("Votre vente aux marchands vous a rapporté <span class='text-vert'>+" . number_format(round($gainSell)) . "</span> bitcoins. Et <span class='text-vert'>+" . number_format($newWarPointS) . "</span> points de Guerre. Votre flotte " . $fleet->getName() . " est sur le chemin du retour.");
                                $em->persist($reportSell);
                                $user->setBitcoin($user->getBitcoin() + $gainSell);
                                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
                                $fleet->setNiobium(0);
                                $fleet->setWater(0);
                                $fleet->setSoldier(0);
                                $fleet->setWorker(0);
                                $fleet->setScientist(0);
                                $server->setNbrSell($server->getNbrSell() + 1);
                                $quest = $user->checkQuests('sell');
                                if ($quest) {
                                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                                    $user->removeQuest($quest);
                                }
                            } else {
                                if ($user != $newPlanet->getUser() && $newPlanet->getUser()) {
                                    $reportSell = new Report();
                                    $reportSell->setSendAt($nowReport);
                                    $reportSell->setUser($newPlanet->getUser());
                                    $reportSell->setTitle("Dépôt de ressources");
                                    $reportSell->setImageName("depot_report.jpg");
                                    $reportSell->setContent("Le joueur " . $newPlanet->getUser()->getUserName() . " vient de déposer des ressources sur votre planète " . $newPlanet->getSector()->getgalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . " " . number_format($fleet->getNiobium()) . " Niobium, " . number_format($fleet->getWater()) . " Eau, " . number_format($fleet->getWorker()) . " Travailleurs, " . number_format($fleet->getSoldier()) . " Soldats, " . number_format($fleet->getScientist()) . " Scientifiques.");
                                    $em->persist($reportSell);
                                }
                                if ($newPlanet->getNiobium() + $fleet->getNiobium() <= $newPlanet->getNiobiumMax()) {
                                    $newPlanet->setNiobium($newPlanet->getNiobium() + $fleet->getNiobium());
                                    $fleet->setNiobium(0);
                                } else {
                                    $fleet->setNiobium($fleet->getNiobium() - ($newPlanet->getNiobiumMax() - $newPlanet->getNiobium()));
                                    $newPlanet->setNiobium($newPlanet->getNiobiumMax());
                                }
                                if ($newPlanet->getWater() + $fleet->getWater() <= $newPlanet->getWaterMax()) {
                                    $newPlanet->setWater($newPlanet->getWater() + $fleet->getWater());
                                    $fleet->setWater(0);
                                } else {
                                    $fleet->setWater($fleet->getWater() - ($newPlanet->getWaterMax() - $newPlanet->getWater()));
                                    $newPlanet->setWater($newPlanet->getWaterMax());
                                }
                                if ($newPlanet->getSoldier() + $fleet->getSoldier() <= $newPlanet->getSoldierMax()) {
                                    $newPlanet->setSoldier($newPlanet->getSoldier() + $fleet->getSoldier());
                                    $fleet->setSoldier(0);
                                } else {
                                    $fleet->setSoldier($fleet->getSoldier() - ($newPlanet->getSoldierMax() - $newPlanet->getSoldier()));
                                    $newPlanet->setSoldier($newPlanet->getSoldierMax());
                                }
                                if ($newPlanet->getWorker() + $fleet->getWorker() <= $newPlanet->getWorkerMax()) {
                                    $newPlanet->setWorker($newPlanet->getWorker() + $fleet->getWorker());
                                    $fleet->setWorker(0);
                                } else {
                                    $fleet->setWorker($fleet->getWorker() - ($newPlanet->getWorkerMax() - $newPlanet->getWorker()));
                                    $newPlanet->setWorker($newPlanet->getWorkerMax());
                                }
                                if ($newPlanet->getScientist() + $fleet->getScientist() <= $newPlanet->getScientistMax()) {
                                    $newPlanet->setScientist($newPlanet->getScientist() + $fleet->getScientist());
                                    $fleet->setScientist(0);
                                } else {
                                    $fleet->setScientist($fleet->getScientist() - ($newPlanet->getScientistMax() - $newPlanet->getScientist()));
                                    $newPlanet->setScientist($newPlanet->getScientistMax());
                                }
                            }

                            $planetTakee = $fleet->getPlanete();
                            $sFleet = $fleet->getPlanet()->getSector()->getPosition();
                            $sector = $oldPlanet->getSector()->getPosition();
                            $galaxy = $oldPlanet->getSector()->getGalaxy()->getPosition();
                            if ($fleet->getPlanet()->getSector()->getGalaxy()->getPosition() != $galaxy) {
                                $base = 18;
                                $price = 25;
                            } else {
                                $pFleet = $fleet->getPlanet()->getPosition();
                                if ($sFleet == $sector) {
                                    $x1 = ($pFleet - 1) % 5;
                                    $x2 = ($planetTakee - 1) % 5;
                                    $y1 = ($pFleet - 1) / 5;
                                    $y2 = ($planetTakee - 1) / 5;
                                } else {
                                    $x1 = (($sFleet - 1) % 10) * 3;
                                    $x2 = (($sector - 1) % 10) * 3;
                                    $y1 = (($sFleet - 1) / 10) * 3;
                                    $y2 = (($sector - 1) / 10) * 3;
                                }
                                $base = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
                                $price = $base / 3;
                            }
                            $carburant = round($price * ($fleet->getNbrSignatures() / 200));
                            $fuser = $fleet->getUser();
                            if ($carburant <= $fuser->getBitcoin()) {
                                if ($fleet->getMotherShip()) {
                                    $speed = $fleet->getSpeed() - ($fleet->getSpeed() * 0.10);
                                } else {
                                    $speed = $fleet->getSpeed();
                                }
                                $distance = $speed * $base * 100;
                                $moreNow = new DateTime();
                                $moreNow->setTimezone(new DateTimeZone('Europe/Paris'));
                                $moreNow->add(new DateInterval('PT' . 120 . 'S'));
                                $nowFlight = new DateTime();
                                $nowFlight->setTimezone(new DateTimeZone('Europe/Paris'));
                                $nowFlight->add(new DateInterval('PT' . round($distance) . 'S'));
                                $fleet->setNewPlanet($oldPlanet->getId());
                                $fleet->setFlightTime($nowFlight);
                                $fleet->setFlightType(1);
                                $fleet->setSector($oldPlanet->getSector());
                                $fleet->setPlanete($oldPlanet->getPosition());
                                $fleet->setCancelFlight($moreNow);
                                $fuser->setBitcoin($user->getBitcoin() - $carburant);
                            }
                        } elseif ($fleet->getFlightType() == '3') {
                            if ($fleet->getColonizer() && $newPlanet->getUser() == null &&
                                $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
                                $newPlanet->getCdr() == false && $fleet->getUser()->getColPlanets() < 26 &&
                                $fleet->getUser()->getColPlanets() <= ($user->getTerraformation() + 1 + $user->getPoliticColonisation())) {

                                $fleet->setColonizer($fleet->getColonizer() - 1);
                                $newPlanet->setUser($fleet->getUser());
                                $newPlanet->setName('Colonie');
                                $newPlanet->setSoldier(50);
                                $newPlanet->setNbColo(count($fleet->getUser()->getPlanets()) + 1);
                                $quest = $fleet->getUser()->checkQuests('colonize');
                                if ($quest) {
                                    $fleet->getUser()->getRank()->setWarPoint($fleet->getUser()->getRank()->getWarPoint() + $quest->getGain());
                                    $fleet->getUser()->removeQuest($quest);
                                }
                                if ($fleet->getNbrShips() == 0) {
                                    $em->remove($fleet);
                                }
                                $reportColo = new Report();
                                $reportColo->setSendAt($nowReport);
                                $reportColo->setUser($user);
                                $reportColo->setTitle("Colonisation de planète");
                                $reportColo->setImageName("colonize_report.jpg");
                                $reportColo->setContent("Vous venez de coloniser une planète inhabitée en : " . "<span><a href='/connect/carte-spatiale/" . $newPlanet->getSector()->getPosition() . "/" . $newPlanet->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $newPlanet->getSector()->getGalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . "</a></span>" . ". Cette planète fait désormais partit de votre Empire, pensez a la renommer sur la page Planètes.");
                                $user->setViewReport(false);
                                $em->persist($reportColo);
                                $server->setNbrColonize($server->getNbrColonize() + 1);
                            }
                        } elseif ($fleet->getFlightType() == '4' && $fleet->getPlanet()->getUser()) {
                            if ($fleet->getUser()->getPoliticBarge() > 0) {
                                $barge = $fleet->getBarge() * 2500 * (1 + ($fleet->getUser()->getPoliticBarge() / 4));
                            } else {
                                $barge = $fleet->getBarge() * 2500;
                            }
                            $defenser = $fleet->getPlanet();
                            $userDefender = $fleet->getPlanet()->getUser();
                            $barbed = $userDefender->getBarbedAdv();
                            $dSoldier = $defenser->getSoldier() > 0 ? ($defenser->getSoldier() * 6) * $barbed : 0;
                            $dTanks = $defenser->getTank() > 0 ? $defenser->getTank() * 300 : 0;
                            $dWorker = $defenser->getWorker();
                            if ($userDefender->getPoliticSoldierAtt() > 0) {
                                $dSoldier = $dSoldier * (1 + ($userDefender->getPoliticSoldierAtt() / 10));
                            }
                            if ($userDefender->getPoliticTankDef() > 0) {
                                $dTanks = $dTanks * (1 + ($userDefender->getPoliticTankDef() / 10));
                            }
                            if ($userDefender->getPoliticWorkerDef() > 0) {
                                $dWorker = $dWorker * (1 + ($userDefender->getPoliticWorkerDef() / 5));
                            }
                            $dMilitary = $dWorker + $dSoldier + $dTanks;
                            $alea = rand(4, 8);

                            $reportInv = new Report();
                            $reportInv->setSendAt($nowReport);
                            $reportInv->setUser($user);
                            $user->setViewReport(false);

                            $reportDef = new Report();
                            $reportDef->setSendAt($nowReport);
                            $reportDef->setUser($userDefender);
                            $userDefender->setViewReport(false);
                            $dSigle = null;
                            if ($userDefender->getAlly()) {
                                $dSigle = $userDefender->getAlly()->getSigle();
                            }

                            if ($barge && $fleet->getPlanet()->getUser() && $fleet->getAllianceUser() && $user->getSigleAllied($dSigle) == NULL) {

                                if ($barge >= $fleet->getSoldier()) {
                                    $aMilitary = $fleet->getSoldier() * $alea;
                                    $soldierAtmp = $fleet->getSoldier();
                                } else {
                                    $aMilitary = $barge * $alea;
                                    $soldierAtmp = $barge;
                                }
                                if ($fleet->getUser()->getPoliticSoldierAtt() > 0) {
                                    $aMilitary = $aMilitary * (1 + ($fleet->getUser()->getPoliticSoldierAtt() / 10));
                                }
                                if ($dMilitary > $aMilitary) {
                                    $warPointDef = round($aMilitary);
                                    $userDefender->getRank()->setWarPoint($userDefender->getRank()->getWarPoint() + $warPointDef);
                                    $aMilitary = ($defenser->getSoldier() * 6) - $aMilitary;
                                    if ($barge < $fleet->getSoldier()) {
                                        $fleet->setSoldier($fleet->getSoldier() - $barge);
                                    }
                                    $defenser->setBarge($defenser->getBarge() + $fleet->getBarge());
                                    $fleet->setBarge(0);
                                    if ($aMilitary < 0) {
                                        $soldierDtmp = $defenser->getSoldier();
                                        $workerDtmp = $defenser->getWorker();
                                        $tankDtmp = $defenser->getTank();
                                        $defenser->setSoldier(0);
                                        $aMilitary = $dTanks - abs($aMilitary);
                                        if ($aMilitary <= 0) {
                                            $defenser->setTank(0);
                                            $defenser->setWorker($defenser->getWorker() + $aMilitary);
                                            $tankDtmp = $tankDtmp - $defenser->getTank();
                                            $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                                            $workerDtmp = $workerDtmp - $defenser->getWorker();
                                        } else {
                                            $defenser->setTank(round($aMilitary / 300));
                                            $tankDtmp = $tankDtmp - $defenser->getTank();
                                            $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                                            $workerDtmp = $workerDtmp - $defenser->getWorker();
                                        }
                                    } else {
                                        $defenser->setSoldier(round($aMilitary / $alea));
                                        $tankDtmp = $defenser->getTank();
                                        $soldierDtmp = round($aMilitary / $alea);
                                        $workerDtmp = $defenser->getWorker();
                                    }
                                    $reportDef->setTitle("Rapport d'invasion : Victoire (défense)");
                                    $reportDef->setImageName("defend_win_report.jpg");
                                    $reportDef->setContent("Bien joué ! Vos travailleurs et soldats ont repoussé l'invasion du joueur " . $user->getUserName() . " sur votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . number_format($soldierAtmp) . " soldats vous ont attaqué, tous ont été tué. Vous avez ainsi prit le contrôle des barges de l'attaquant.<br>Et vous remportez <span class='text-vert'>+" . number_format($warPointDef) . "</span> points de Guerre.");
                                    $reportInv->setTitle("Rapport d'invasion : Défaite (attaque)");
                                    $reportInv->setImageName("invade_lose_report.jpg");
                                    $reportInv->setContent("'AH AH AH AH' le rire de " . $userDefender->getUserName() . " résonne à vos oreilles d'un curieuse façon. Votre sang bouillonne vous l'a vouliez cette planète. Qu'il rigole donc, vous reviendrez prendre " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " et ferez effacer des livres d'histoires son ridicule nom. Vous avez tout de même tué " . number_format($soldierDtmp) . " soldats, " . number_format($tankDtmp) . " tanks et " . number_format($workerDtmp) . " travailleurs à l'ennemi. Tous vos soldats sont morts et vos barges sont resté sur la planète. Courage commandant.");
                                } else {
                                    $soldierDtmp = $defenser->getSoldier();
                                    $workerDtmp = $defenser->getWorker();
                                    $tankDtmp = $defenser->getTank();
                                    $warPointAtt = round($soldierDtmp + ($workerDtmp / 10));
                                    $fleet->getUser()->getRank()->setWarPoint($fleet->getUser()->getRank()->getWarPoint() + $warPointAtt);
                                    $soldierAtmp = $fleet->getSoldier();
                                    $fleet->setSoldier(($aMilitary - $dMilitary) / $alea);
                                    $soldierAtmp = $soldierAtmp - $fleet->getSoldier();
                                    $defenser->setSoldier(0);
                                    $defenser->setTank(0);
                                    $defenser->setWorker(2000);
                                    if ($fleet->getUser()->getColPlanets() <= ($fleet->getUser()->getTerraformation() + 1 + $fleet->getUser()->getPoliticInvade())) {
                                        $defenser->setUser($fleet->getUser());
                                        $em->flush();
                                    } else {
                                        $hydra = $em->getRepository('App:User')->findOneBy(['zombie' => 1]);

                                        if ($defenser->getZombie() == 1) {
                                            $image = [
                                                'planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png',
                                                'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png',
                                                'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png',
                                                'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png',
                                                'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png',
                                                'planet31.png', 'planet32.png', 'planet33.png'
                                            ];
                                            $defenser->setUser(null);
                                            $defenser->setName('Inhabitée');
                                            $defenser->setImageName($image[rand(0, 32)]);
                                        } else {
                                            $defenser->setUser($hydra);
                                            $defenser->setWorker(125000);
                                            if ($defenser->getSoldierMax() >= 2500) {
                                                $defenser->setSoldier($defenser->getSoldierMax());
                                            } else {
                                                $defenser->setCaserne(1);
                                                $defenser->setSoldier(2500);
                                                $defenser->setSoldierMax(2500);
                                            }
                                            $defenser->setName('Base Zombie');
                                            $defenser->setImageName('hydra_planet.png');
                                            $em->flush();
                                        }
                                    }
                                    if ($userDefender->getAllPlanets() == 0) {
                                        $userDefender->setGameOver($fleet->getUser()->getUserName());
                                        $userDefender->setAlly(null);
                                        $userDefender->setGrade(null);
                                        foreach ($userDefender->getFleets() as $tmpFleet) {
                                            $tmpFleet->setUser($fleet->getUser());
                                        }
                                    }
                                    $reportDef->setTitle("Rapport d'invasion : Défaite (défense)");
                                    $reportDef->setImageName("defend_lose_report.jpg");
                                    $reportDef->setContent("Mais QUI ? QUI !!! Vous as donné un commandant si médiocre " . $fleet->getUser()->getUserName() . " n'a pas eu a faire grand chose pour prendre votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . round($soldierAtmp) . " soldats ennemis sont tout de même éliminé. C'est toujours ça de gagner. Vos " . number_format($soldierDtmp) . " soldats, " . number_format($tankDtmp) . " tanks et " . number_format($workerDtmp) . " travailleurs sont tous mort. Votre empire en a prit un coup, mais il vous reste des planètes, il est l'heure de la revanche !");
                                    $reportInv->setTitle("Rapport d'invasion : Victoire (attaque)");
                                    $reportInv->setImageName("invade_win_report.jpg");
                                    $reportInv->setContent("Vous débarquez après que la planète ait été prise et vous installez sur le trône de " . $userDefender->getUserName() . ". Qu'il est bon d'entendre ses pleures lointain... La planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " est désormais votre! Il est temps de remettre de l'ordre dans la galaxie. " . number_format(round($soldierAtmp)) . " de vos soldats ont péri dans l'invasion. Mais les défenseurs ont aussi leurs pertes : " . number_format($soldierDtmp) . " soldats et " . number_format($workerDtmp) . " travailleurs ont péri. Cependant vous épargnez 2000 travailleurs dans votre bonté (surtout pour faire tourner la planète).<br>Et vous remportez <span class='text-vert'>+" . number_format($warPointAtt) . "</span> points de Guerre.");
                                    $quest = $fleet->getUser()->checkQuests('invade');
                                    if ($quest) {
                                        $fleet->getUser()->getRank()->setWarPoint($fleet->getUser()->getRank()->getWarPoint() + $quest->getGain());
                                        $fleet->getUser()->removeQuest($quest);
                                    }
                                }
                                if ($fleet->getNbrShips() == 0) {
                                    $em->remove($fleet);
                                }
                                $em->persist($reportInv);
                                $em->persist($reportDef);
                                $server->setNbrInvasion($server->getNbrInvasion() + 1);
                            }
                        }
                    } else {
                        $em->persist($report);
                    }
                }
            }
        }
        $em->flush();
        exit;
    }

    /**
     * @Route("/repare/", name="repare_it")
     */
    public function repareAction()
    {
        $em = $this->getDoctrine()->getManager();

        $iaPlayer = new User();
        $now = new DateTime();
        $iaPlayer->setUsername('Marchands');
        $iaPlayer->setEmail('support+5@areauniverse.eu');
        $iaPlayer->setCreatedAt($now);
        $iaPlayer->setPassword(password_hash('ViolGratuit2019', PASSWORD_BCRYPT));
        $iaPlayer->setBitcoin(100);
        $iaPlayer->setImageName('merchant.png');
        $iaPlayer->setMerchant(1);
        $rank = new Rank();
        $em->persist($rank);
        $iaPlayer->setRank($rank);
        $em->persist($iaPlayer);

        $iaSalon = $em->getRepository('App:Salon')->find(['id' => 1]);
        $iaSalon->addUser($iaPlayer);
        $em->flush();
        exit;
    }
}
