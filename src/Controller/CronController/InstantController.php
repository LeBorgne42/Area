<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Report;
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

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user is not null')
            ->andWhere('p.niobiumMax > (p.niobium + p.nbProduction) or p.waterMax > (p.water + p.wtProduction)')
            ->getQuery()
            ->getResult();

        foreach ($planets as $planet) {
            $niobium = $planet->getNiobium();
            $water = $planet->getWater();
            $niobium = $niobium + ($planet->getNbProduction());
            $water = $water + ($planet->getWtProduction());
            if($planet->getNiobiumMax() > ($planet->getNiobium() + $planet->getNbProduction())) {
                $planet->setNiobium($niobium);
            } else {
                $planet->setNiobium($planet->getNiobiumMax());
            }
            if($planet->getWaterMax() > ($planet->getWater() + $planet->getWtProduction())) {
                $planet->setWater($water);
            } else {
                $planet->setWater($planet->getWaterMax());
            }
            $em->persist($planet);
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
        if(time() % 3600 == 0) {
            $asteroides = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.cdr = :true')
                ->setParameters(array('true' => true))
                ->getQuery()
                ->getResult();
            if($asteroides) {
                foreach ($asteroides as $asteroide) {
                    $nbrFleet = $asteroide->getFleetWithRec();
                    foreach ($asteroide->getFleets() as $fleetAsteroide) {
                        $asteroideRes = round(25000 / $nbrFleet);
                        if ($fleetAsteroide->getRecycleur()) {
                            if ($fleetAsteroide->getCargoPlace() < ($fleetAsteroide->getCargoFull() + ($asteroideRes * 2))) {
                                $cargoFullAst = round((($fleetAsteroide->getCargoPlace() - $fleetAsteroide->getCargoFull()) / 2));
                                $fleetAsteroide->setNiobium($fleetAsteroide->getNiobium() + $cargoFullAst);
                                $fleetAsteroide->setWater($fleetAsteroide->getWater() + $cargoFullAst);
                            } else {
                                $fleetAsteroide->setNiobium($fleetAsteroide->getNiobium() + $asteroideRes);
                                $fleetAsteroide->setWater($fleetAsteroide->getWater() + $asteroideRes);
                            }
                        }
                        $em->persist($fleetAsteroide);
                    }
                    if(rand(1, 300) == 300) {
                        $asteroide->setCdr(false);
                        $asteroide->setEmpty(true);
                        $asteroide->setImageName(null);
                        $asteroide->setName('Vide');
                        $newAsteroides = $em->getRepository('App:Planet')
                            ->createQueryBuilder('p')
                            ->join('p.sector', 's')
                            ->where('p.empty = :true')
                            ->andWhere('s.id = :rand')
                            ->setParameters(array('true' => true, 'rand' => rand(1, 100)))
                            ->setMaxResults(1)
                            ->getQuery()
                            ->getResult();
                        foreach($newAsteroides as $newAsteroide) {
                            $newAsteroide->setEmpty(false);
                            $newAsteroide->setCdr(true);
                            $newAsteroide->setImageName('cdr.png');
                            $newAsteroide->setName('Astéroïdes');
                        }
                    }
                }
            }
        }
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.searchAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $planetSoldiers = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.soldierAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $planetScientists = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.scientistAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.flightTime < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $fleetCdrs = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.recycleAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.constructAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $products = $em->getRepository('App:Product')
            ->createQueryBuilder('p')
            ->where('p.productAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $radars = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.radarAt < :now or p.brouilleurAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $pacts = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.dismissAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        $peaces = $em->getRepository('App:Peace')
            ->createQueryBuilder('p')
            ->where('p.signedAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        foreach ($peaces as $peace) {
            $em->remove($peace);
            $em->flush();
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
                ->setParameters(array(
                    'allytag' => $pact->getDismissBy(),
                    'ally' => $otherAlly))
                ->getQuery()
                ->getOneOrNullResult();

            $salons = $em->getRepository('App:Salon')
                ->createQueryBuilder('s')
                ->where('s.name = :sigle1')
                ->orWhere('s.name = :sigle2')
                ->setParameters(array('sigle1' => $otherAlly->getSigle() . " - " . $pact->getDismissBy(), 'sigle2' => $pact->getDismissBy() . " - " . $otherAlly->getSigle()))
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
            $em->flush();
        }

        foreach ($planetSoldiers as $soldierAt) {
            $soldierAt->setSoldier($soldierAt->getSoldier() + $soldierAt->getSoldierAtNbr());
            $soldierAt->setSoldierAt(null);
            $soldierAt->setSoldierAtNbr(null);
            $em->persist($soldierAt);
        }
        foreach ($planetScientists as $scientistAt) {
            $scientistAt->setScientist($scientistAt->getScientist() + $scientistAt->GetScientistAtNbr());
            $scientistAt->getUser()->setScientistProduction(round($scientistAt->getUser()->getScientistProduction() + ($scientistAt->getScientist() / 10000)));
            $scientistAt->setScientistAt(null);
            $scientistAt->setScientistAtNbr(null);
            $em->persist($scientistAt);
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

        foreach ($fleetCdrs as $fleetCdr) {
            $recycle = $fleetCdr->getRecycleur() * 5000;
            $planetCdr = $fleetCdr->getPlanet();
            if ($fleetCdr->getCargoPlace() < ($fleetCdr->getCargoFull() + ($recycle * 2))) {
                $cargoFullCdr = round((($fleetCdr->getCargoPlace() - $fleetCdr->getCargoFull()) / 2));
                if ($planetCdr->getNbCdr() > $cargoFullCdr) {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $cargoFullCdr);
                    $planetCdr->setNbCdr($planetCdr->getNbCdr() - $cargoFullCdr);
                } else {
                    $fleetCdr->setNiobium($fleetCdr->getNiobium() + $planetCdr->getNbCdr());
                    $planetCdr->setNbCdr(0);
                }
                if ($planetCdr->getWtCdr() > $cargoFullCdr) {
                    $fleetCdr->setWater($fleetCdr->getWater() + $cargoFullCdr);
                    $planetCdr->setWtCdr($planetCdr->getWtCdr() - $cargoFullCdr);
                } else {
                    $fleetCdr->setWater($fleetCdr->getWater() + $planetCdr->getWtCdr());
                    $planetCdr->setWtCdr(0);
                }
                if($planetCdr->getNbCdr() == 0 && $planetCdr->getWtCdr() == 0) {
                    $fleetCdr->setRecycleAt(null);
                }
            } else {
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
                if (($planetCdr->getNbCdr() > 0 || $planetCdr->getWtCdr() > 0) && $fleetCdr->getCargoPlace() > $fleetCdr->getCargoFull()) {
                    $tmpNoCdr = $now;
                    $tmpNoCdr->add(new DateInterval('PT' . 3600 . 'S'));
                    $fleetCdr->setRecycleAt($now);
                } else {
                    $fleetCdr->setRecycleAt(null);
                }
            }
            $em->persist($fleetCdr);
            $em->persist($planetCdr);
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
                $planet->setNiobiumMax($planet->getNiobiumMax() + 750000);
            } elseif ($build == 'waterStock') {
                $planet->setWaterStock($planet->getWaterStock() + 1);
                $planet->setWaterMax($planet->getWaterMax() + 750000);
            } elseif ($build == 'city') {
                $planet->setCity($planet->getCity() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 2000);
                $planet->setWorkerMax($planet->getWorkerMax() + 25000);
            } elseif ($build == 'metropole') {
                $planet->setMetropole($planet->getMetropole() + 1);
                $planet->setWorkerProduction($planet->getWorkerProduction() + 5000);
                $planet->setWorkerMax($planet->getWorkerMax() + 75000);
            } elseif ($build == 'caserne') {
                $planet->setCaserne($planet->getCaserne() + 1);
                $planet->setSoldierMax($planet->getSoldierMax() + 2500);
            } elseif ($build == 'bunker') {
                $planet->setBunker($planet->getBunker() + 1);
                $planet->setSoldierMax($planet->getSoldierMax() + 20000);
            } elseif ($build == 'centerSearch') {
                $planet->setCenterSearch($planet->getCenterSearch() + 1);
                $planet->setScientistMax($planet->getScientistMax() + 500);
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
            if ($research == 'onde') {
                $user->setOnde($user->getOnde() + 1);
            } elseif ($research == 'industry') {
                $user->setIndustry($user->getIndustry() + 1);
            } elseif ($research == 'discipline') {
                $user->setDiscipline($user->getDiscipline() + 1);
            } elseif ($research == 'hyperespace') {
                $user->setHyperespace(1);
            } elseif ($research == 'barge') {
                $user->setBarge(1);
            } elseif ($research == 'utility') {
                $user->setUtility($user->getUtility() + 1);
            } elseif ($research == 'demography') {
                $user->setDemography($user->getDemography() + 1);
            } elseif ($research == 'terraformation') {
                $user->setTerraformation($user->getTerraformation() + 1);
            } elseif ($research == 'cargo') {
                $user->setCargo($user->getCargo() + 1);
            } elseif ($research == 'recycleur') {
                $user->setRecycleur(1);
            } elseif ($research == 'armement') {
                $user->setArmement($user->getArmement() + 1);
            } elseif ($research == 'missile') {
                $user->setMissile($user->getMissile() + 1);
            } elseif ($research == 'laser') {
                $user->setLaser($user->getLaser() + 1);
            } elseif ($research == 'plasma') {
                $user->setPlasma($user->getPlasma() + 1);
            } elseif ($research == 'lightShip') {
                $user->setLightShip($user->getLightShip() + 1);
            } elseif ($research == 'heavyShip') {
                $user->setHeavyShip($user->getHeavyShip() + 1);
            }
            $user->setSearch(null);
            $user->setSearchAt(null);
            $em->persist($user);
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
            $em->persist($planetProduct);
            $em->remove($product);
        }

        foreach ($fleets as $fleet) {

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

            $userFleet = $fleet->getUser();
            $report = new Report();
            $report->setTitle("Votre flotte " . $fleet->getName() . " est arrivée");
            $report->setSendAt($now);
            $report->setUser($userFleet);
            $report->setContent("Bonjour dirigeant " . $userFleet->getUserName() . " votre flotte " . $fleet->getName() . " vient d'arriver en " . $newHome->getSector()->getGalaxy()->getPosition() . ":" . $newHome->getSector()->getPosition() . ":" . $newHome->getPosition() . ".");
            $userFleet->setViewReport(false);
            $em->persist($userFleet);
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
                if($tmp->getAccepted() == 1) {
                    $friendAlly[$x] = $tmp->getAllyTag();
                    $x++;
                }
            }
            if(!$friendAlly) {
                $friendAlly = ['impossible', 'personne'];
            }

            $warFleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.user', 'u')
                ->leftJoin('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.attack = :true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->andWhere('f.flightTime is null')
                ->andWhere('u.ally is null OR a.sigle not in (:friend)')
                ->setParameters(array('planet' => $newHome, 'true' => true, 'ally' => $warAlly, 'user' => $user, 'friend' => $friendAlly))
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
                ->setParameters(array('planet' => $newHome, 'user' => $user, 'friend' => $friendAlly))
                ->getQuery()
                ->getResult();

            $fleetFight = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.user', 'u')
                ->leftJoin('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.fightAt is not null')
                ->andWhere('f.flightTime is null')
                ->setParameters(array('planet' => $newHome))
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            if($fleetFight) {
                $fleet->setFightAt($fleetFight->getFightAt());
            } elseif ($warFleets) {
                foreach ($warFleets as $setWar) {
                    if($setWar->getUser()->getAlly()) {
                        $fleetArm = $fleet->getMissile() + $fleet->getLaser() + $fleet->getPlasma();
                        if($fleetArm > 0) {
                            $fleet->setAttack(1);
                        }
                        foreach ($eAlly as $tmp) {
                            if ($setWar->getUser()->getAlly()->getSigle() == $tmp->getAllyTag()) {
                                $fleetArm = $setWar->getMissile() + $setWar->getLaser() + $setWar->getPlasma();
                                if($fleetArm > 0) {
                                    $setWar->setAttack(1);
                                }
                                $em->persist($setWar);
                            }
                        }
                    }
                }
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.flightTime is null')
                    ->setParameters(array('planet' => $newHome))
                    ->getQuery()
                    ->getResult();

                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));

                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                    $em->persist($updateF);
                }
                $fleet->setFightAt($now);
                $report->setContent($report->getContent() . " Attention votre flotte est rentrée en combat !");
            } elseif ($neutralFleets && $fleet->getAttack() == 1) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.flightTime is null')
                    ->setParameters(array('planet' => $newHome))
                    ->getQuery()
                    ->getResult();

                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));

                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                    $em->persist($updateF);
                }
                $fleet->setFightAt($now);
                $report->setContent($report->getContent() . " Attention votre flotte est rentrée en combat !");
            }
            $em->persist($report);
            $em->persist($fleet);
            if ($fleet->getFightAt() == null) {
                $user = $fleet->getUser();
                $newPlanet = $fleet->getPlanet();
                
                if ($fleet->getFlightType() == '2') {
                    if($newPlanet->getMerchant() == true) {
                        $reportSell = new Report();
                        $reportSell->setSendAt($now);
                        $reportSell->setUser($user);
                        $reportSell->setTitle("Vente aux marchands");
                        $newWarPointS = ((($fleet->getScientist() * 100) + ($fleet->getWorker() * 50) + ($fleet->getSoldier() * 10) + ($fleet->getWater() / 3) + ($fleet->getNiobium() / 6)) / 400);
                        $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleet->getWater() * 0.5) + ($fleet->getSoldier() * 5) + ($fleet->getWorker() * 2) + ($fleet->getScientist() * 50) + ($fleet->getNiobium() * 0.25)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
                        $em->persist($reportSell);
                        $user->setBitcoin($user->getBitcoin() + ($fleet->getWater() * 0.5) + ($fleet->getSoldier() * 5) + ($fleet->getWorker() * 2) + ($fleet->getScientist() * 50) + ($fleet->getNiobium() * 0.25));
                        $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
                        $fleet->setNiobium(0);
                        $fleet->setWater(0);
                        $fleet->setSoldier(0);
                        $fleet->setWorker(0);
                        $fleet->setScientist(0);
                    } else {
                        if($user != $newPlanet->getUser() && $newPlanet->getUser()) {
                            $reportSell = new Report();
                            $reportSell->setSendAt($now);
                            $reportSell->setUser($newPlanet->getUser());
                            $reportSell->setTitle("Dépôt de ressources");
                            $reportSell->setContent("Le joueur " . $newPlanet->getUser()->getUserName() . " vient de déposer des ressources sur votre planète "  . $newPlanet->getSector()->getgalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . " " . $fleet->getNiobium() . " Niobium, " . $fleet->getWater() . " Eau, " . $fleet->getWorker() . " Travailleurs, " . $fleet->getSoldier() . " Soldats, " . $fleet->getScientist() . " Scientifiques.");
                            $em->persist($reportSell);
                        }
                        if($newPlanet->getNiobium() + $fleet->getNiobium() <= $newPlanet->getNiobiumMax()) {
                            $newPlanet->setNiobium($newPlanet->getNiobium() + $fleet->getNiobium());
                            $fleet->setNiobium(0);
                        } else {
                            $fleet->setNiobium($fleet->getNiobium() - ($newPlanet->getNiobiumMax() - $newPlanet->getNiobium()));
                            $newPlanet->setNiobium($newPlanet->getNiobiumMax());
                        }
                        if($newPlanet->getWater() + $fleet->getWater() <= $newPlanet->getWaterMax()) {
                            $newPlanet->setWater($newPlanet->getWater() + $fleet->getWater());
                            $fleet->setWater(0);
                        } else {
                            $fleet->setWater($fleet->getWater() - ($newPlanet->getWaterMax() - $newPlanet->getWater()));
                            $newPlanet->setWater($newPlanet->getWaterMax());
                        }
                        if($newPlanet->getSoldier() + $fleet->getSoldier() <= $newPlanet->getSoldierMax()) {
                            $newPlanet->setSoldier($newPlanet->getSoldier() + $fleet->getSoldier());
                            $fleet->setSoldier(0);
                        } else {
                            $fleet->setSoldier($fleet->getSoldier() - ($newPlanet->getSoldierMax() - $newPlanet->getSoldier()));
                            $newPlanet->setSoldier($newPlanet->getSoldierMax());
                        }
                        if($newPlanet->getWorker() + $fleet->getWorker() <= $newPlanet->getWorkerMax()) {
                            $newPlanet->setWorker($newPlanet->getWorker() + $fleet->getWorker());
                            $fleet->setWorker(0);
                        } else {
                            $fleet->setWorker($fleet->getWorker() - ($newPlanet->getWorkerMax() - $newPlanet->getWorker()));
                            $newPlanet->setWorker($newPlanet->getWorkerMax());
                        }
                        if($newPlanet->getScientist() + $fleet->getScientist() <= $newPlanet->getScientistMax()) {
                            $newPlanet->setScientist($newPlanet->getScientist() + $fleet->getScientist());
                            $fleet->setScientist(0);
                        } else {
                            $fleet->setScientist($fleet->getScientist() - ($newPlanet->getScientistMax() - $newPlanet->getScientist()));
                            $newPlanet->setScientist($newPlanet->getScientistMax());
                        }
                    }

                    $planetTakee = $fleet->getPlanete();
                    $sFleet= $fleet->getPlanet()->getSector()->getPosition();
                    $sector = $oldPlanet->getSector()->getPosition();
                    if ($sFleet == $sector) {
                        $pFleet = $fleet->getPlanet()->getPosition();
                        if (strpos('0 -1 1 -4 4 -5 5 6 -6', (strval($pFleet - $planetTakee)) ) != false) {
                            $base = 1500;
                        } elseif (strpos('2 -2 3 -3 7 -7 8 -8 9 -9 10 -10 11 -11 12 -12', (strval($pFleet - $planetTakee)) ) != false) {
                            $base = 1750;
                        } else {
                            $base = 2000;
                        }
                    }
                    /*elseif (strpos('0 -1 1 -10 10 -9 9', (strval($sFleet - $sector)) ) != false) {
                        $base= 3000;
                    } elseif (strpos('-20 20 12 11 8 2 -12 -11 -8 -2', (strval($sFleet - $sector)) ) != false) {
                        $base= 6800;
                    } elseif (strpos('-28 28 29 30 31 32 33 22 12 3 7 -29 -30 -31 -32 -33 -22 -13 -3 -7', (strval($sFleet - $sector)) ) != false) {
                        $base= 8000;
                    } else {
                        $base= 15000;
                    }*/
                    else {
                        $base = 3000;
                    }
                    if($fleet->getMotherShip()) {
                        $speed = $fleet->getSpeed()  - ($fleet->getSpeed() * 0.10);
                    } else {
                        $speed = $fleet->getSpeed();
                    }
                    $now->add(new DateInterval('PT' . round($speed * $base) . 'S'));
                    $fleet->setNewPlanet($oldPlanet->getId());
                    $fleet->setFlightTime($now);
                    $fleet->setFlightType(1);
                    $fleet->setSector($oldPlanet->getSector());
                    $fleet->setPlanete($oldPlanet->getPosition());
                    $em->persist($fleet);
                    $em->persist($newPlanet);
                    $em->flush();
                } elseif ($fleet->getFlightType() == '3') {
                    if ($fleet->getColonizer() && $newPlanet->getUser() == null &&
                        $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
                        $newPlanet->getCdr() == false && $fleet->getUser()->getColPlanets() < 21 &&
                        $fleet->getUser()->getColPlanets() <= ($user->getTerraformation() + 1)) {
                        $fleet->setColonizer($fleet->getColonizer() - 1);
                        $newPlanet->setUser($fleet->getUser());
                        $newPlanet->setName('Colonie');
                        $em->persist($fleet);
                        if ($fleet->getNbrShips() == 0) {
                            $em->remove($fleet);
                        }
                        $reportColo = new Report();
                        $reportColo->setSendAt($now);
                        $reportColo->setUser($user);
                        $reportColo->setTitle("Colonisation de planète");
                        $reportColo->setContent("Vous venez de coloniser une planète inhabitée en : " .  $newPlanet->getSector()->getgalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . ". Cette planète fait désormais partit de votre Empire, pensez a la renommer sur la page Planètes.");
                        $user->setViewReport(false);
                        $em->persist($reportColo);
                        $em->persist($user);
                        $em->persist($newPlanet);
                        $em->flush();
                    }
                } elseif ($fleet->getFlightType() == '4' && $fleet->getPlanet()->getUser()) {
                    $barge = $fleet->getBarge() * 2500;
                    $defenser = $fleet->getPlanet();
                    $userDefender= $fleet->getPlanet()->getUser();
                    $dMilitary = $defenser->getWorker() + ($defenser->getSoldier() * 6);
                    $alea = rand(4, 8);

                    $reportInv = new Report();
                    $reportInv->setSendAt($now);
                    $reportInv->setUser($user);
                    $user->setViewReport(false);

                    $reportDef = new Report();
                    $reportDef->setSendAt($now);
                    $reportDef->setUser($userDefender);
                    $userDefender->setViewReport(false);
                    $dSigle = null;
                    if($userDefender->getAlly()) {
                        $dSigle = $userDefender->getAlly()->getSigle();
                    }

                    if($barge && $fleet->getPlanet()->getUser() && $fleet->getAllianceUser() && $user->getSigleAlliedArray($dSigle)) {

                        if($barge >= $fleet->getSoldier()) {
                            $aMilitary = $fleet->getSoldier() * $alea;
                            $soldierAtmp = $fleet->getSoldier();
                        } else {
                            $aMilitary = $barge * $alea;
                            $soldierAtmp = $barge;
                        }
                        if($dMilitary > $aMilitary) {
                            $aMilitary = ($defenser->getSoldier() * 6) - $aMilitary;
                            if($barge < $fleet->getSoldier()) {
                                $fleet->setSoldier($fleet->getSoldier() - $barge);
                            }
                            $defenser->setBarge($defenser->getBarge() + $fleet->getBarge());
                            $fleet->setBarge(0);
                            if($aMilitary < 0) {
                                $soldierDtmp = $defenser->getSoldier();
                                $workerDtmp = $defenser->getWorker();
                                $defenser->setSoldier(0);
                                $defenser->setWorker($defenser->getWorker() + $aMilitary);
                                $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                                $workerDtmp = $workerDtmp - $defenser->getWorker();
                            } else {
                                $defenser->setSoldier($aMilitary / 6);
                                $soldierDtmp = round($aMilitary / 6);
                                $workerDtmp = $defenser->getWorker();
                            }
                            $reportDef->setTitle("Rapport d'invasion : Victoire (défense)");
                            $reportDef->setContent("Bien joué ! Vos travailleurs et soldats ont repoussé l'invasion du joueur " . $user->getUserName() . " sur votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . $soldierAtmp . " soldats vous ont attaqué, tous ont été tué. Vous avez ainsi prit le contrôle des barges de l'attaquant.");
                            $reportInv->setTitle("Rapport d'invasion : Défaite (attaque)");
                            $reportInv->setContent("'AH AH AH AH' le rire de " . $userDefender->getUserName() . " résonne à vos oreilles d'un curieuse façon. Votre sang bouillonne vous l'a vouliez cette planète. Qu'il rigole donc, vous reviendrez prendre " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " et ferez effacer des livres d'histoires son ridicule nom. Vous avez tout de même tué " . $soldierDtmp . " soldats et " . $workerDtmp . " travailleurs à l'ennemi. Tous vos soldats sont morts et vos barges sont resté sur la planète. Courage commandant.");
                        } else {
                            $soldierDtmp = $defenser->getSoldier();
                            $workerDtmp = $defenser->getWorker();
                            $soldierAtmp = $fleet->getSoldier();
                            $fleet->setSoldier(($aMilitary - $dMilitary) / $alea);
                            $soldierAtmp = $soldierAtmp - $fleet->getSoldier();
                            $defenser->setSoldier(0);
                            $defenser->setWorker(2000);
                            if($fleet->getUser()->getColPlanets() <= ($fleet->getUser()->getTerraformation() + 1)) {
                                $defenser->setUser($user);
                                $em->persist($defenser);
                                $em->flush();
                            } else {
                                $hydra = $em->getRepository('App:User')
                                    ->createQueryBuilder('u')
                                    ->where('u.id = :id')
                                    ->setParameters(array('id' => 1))
                                    ->getQuery()
                                    ->getOneOrNullResult();

                                $defenser->setUser($hydra);
                                $defenser->setWorker(25000);
                                $defenser->setSoldier(500);
                                $defenser->setName('Avant Poste');
                                $em->persist($defenser);
                                $em->flush();
                            }
                            if($userDefender->getAllPlanets() == 0) {
                                $userDefender->setGameOver($user->getUserName());
                                $userDefender->setAlly(null);
                                $userDefender->setGrade(null);
                                foreach($userDefender->getFleets() as $tmpFleet) {
                                    $tmpFleet->setUser($user);
                                    $em->persist($tmpFleet);
                                }
                                $em->persist($userDefender);
                            }
                            $reportDef->setTitle("Rapport d'invasion : Défaite (défense)");
                            $reportDef->setContent("Mais QUI ? QUI !!! Vous as donné un commandant si médiocre " . $user->getUserName() . " n'a pas eu a faire grand chose pour prendre votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . round($soldierAtmp) . " soldats ennemis sont tout de même éliminé. C'est toujours ça de gagner. Vos " . $soldierDtmp . " soldats et " . $workerDtmp . " travailleurs sont tous mort. Votre empire en a prit un coup, mais il vous reste des planètes, il est l'heure de la revanche !");
                            $reportInv->setTitle("Rapport d'invasion : Victoire (attaque)");
                            $reportInv->setContent("Vous débarquez après que la planète ait été prise et vous installez sur le trône de " . $userDefender->getUserName() . ". Qu'il est bon d'entendre ses pleures lointain... La planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " est désormais votre! Il est temps de remettre de l'ordre dans la galaxie. " . round($soldierAtmp) . " de vos soldats ont péri dans l'invasion. Mais les défenseurs ont aussi leurs pertes : " . $soldierDtmp . " soldats et " . $workerDtmp . " travailleurs ont péri. Cependant vous épargnez 2000 travailleurs dans votre bonté (surtout pour faire tourner la planète).");
                        }
                        $em->persist($fleet);
                        if($fleet->getNbrShips() == 0) {
                            $em->remove($fleet);
                        }
                        $em->persist($reportInv);
                        $em->persist($reportDef);
                        $em->flush();
                    }
                }
            }
        }
        $em->flush();
        exit;
    }
}
