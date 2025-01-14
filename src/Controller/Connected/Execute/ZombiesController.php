<?php

namespace App\Controller\Connected\Execute;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Destination;
use App\Entity\Report;
use App\Entity\Fleet;
use DateInterval;
use DateTime;

/**
 * Class ZombiesController
 * @package App\Controller\Connected\Execute
 */
class ZombiesController extends AbstractController
{
    /**
     * @param $zCommanders
     * @param $now
     * @param $em
     * @return Response
     * @throws Exception
     */
    public function zombiesAction($zCommanders, $now, $em): Response
    {
        foreach ($zCommanders as $zCommander) {
            $usePlanet = $doctrine->getRepository(Planet::class)->findByFirstPlanet($zCommander);
            $zombie = $doctrine->getRepository(Commander::class)->findOneBy(['zombie' => 1]);

            $planetAtt = $doctrine->getRepository(Planet::class)
                ->createQueryBuilder('p')
                ->where('p.commander = :commander')
                ->andWhere('p.radarAt is null and p.brouilleurAt is null')
                ->setParameters(['commander' => $zCommander])
                ->orderBy('p.ground', 'ASC')
                ->getQuery()
                ->setMaxresults(1)
                ->getOneOrNullResult();

            $planetZb = $doctrine->getRepository(Planet::class)
                ->createQueryBuilder('p')
                ->where('p.commander = :commander')
                ->setParameters(['commander' => $zombie])
                ->orderBy('p.ground', 'DESC')
                ->getQuery()
                ->setMaxresults(1)
                ->getOneOrNullResult();

            if (!$planetAtt) {
                echo "Attaques zombies impossible - ";

                return new Response ("KO<br/>");
            }

            if ($zCommander->getZombieLvl() > 0) {
                $reportDef = new Report();
                $reportDef->setType('invade');
                $reportDef->setSendAt($now);
                $reportDef->setCommander($zCommander);
                if ($zCommander->getZombieLvl() >= 1 && $zCommander->getUser()->getTutorial() == 50) {
                    $zCommander->getUser()->setTutorial(51);
                }

                $barbed = $zCommander->getBarbedAdv();
                $dSoldier = $planetAtt->getSoldier() > 0 ? ($planetAtt->getSoldier() * 6) * $barbed : 0;
                $dTanks = $planetAtt->getTank() > 0 ? $planetAtt->getTank() * 3000 : 0;
                $dWorker = $planetAtt->getWorker();
                if ($zCommander->getPoliticSoldierAtt() > 0) {
                    $dSoldier = $dSoldier * (1 + ($zCommander->getPoliticSoldierAtt() / 10));
                }
                if ($zCommander->getPoliticTankDef() > 0) {
                    $dTanks = $dTanks * (1 + ($zCommander->getPoliticTankDef() / 10));
                }
                if ($zCommander->getPoliticWorkerDef() > 0) {
                    $dWorker = $dWorker * (1 + ($zCommander->getPoliticWorkerDef() / 5));
                }
                $dMilitary = $dWorker + $dSoldier + $dTanks;
                $aMilitary = (500 * (($zCommander->getZombieLvl() / 2) + 1) * 2 * round(1 + ($zCommander->getTerraformation()) / 5));
                $soldierAtmp = (500 * (($zCommander->getZombieLvl() / 2) + 1));

                if ($dMilitary > $aMilitary) {
                    if ($zCommander->getAlliance()) {
                        if ($zCommander->getAlliance()->getPolitic() == 'fascism') {
                            $zCommander->setZombieLvl($zCommander->getZombieLvl() + 150);
                        } else {
                            $zCommander->setZombieLvl($zCommander->getZombieLvl() + 200);
                        }
                    } else {
                        $zCommander->setZombieLvl($zCommander->getZombieLvl() + 100);
                    }
                    $warPointDef = round($aMilitary / 10);
                    $zCommander->getRank()->setWarPoint($zCommander->getRank()->getWarPoint() + $warPointDef);
                    $aMilitary = $dSoldier - $aMilitary;
                    $reportDef->setType("zombie");
                    $reportDef->setTitle("Rapport invasion zombies : Victoire");
                    $reportDef->setImageName("zombie_win_report.webp");
                    $soldierDtmp = $planetAtt->getSoldier();
                    $workerDtmp = $planetAtt->getWorker();
                    $tankDtmp = $planetAtt->getTank();
                    if ($aMilitary <= 0) {
                        $planetAtt->setSoldier(0);
                        $aMilitary = $dTanks - abs($aMilitary);
                        if ($aMilitary <= 0) {
                            $planetAtt->setTank(0);
                            $planetAtt->setWorker($planetAtt->getWorker() + ($aMilitary / (1 + ($zCommander->getPoliticWorkerDef() / 5))));
                            $workerDtmp = $workerDtmp - $planetAtt->getWorker();
                            $reportDef->setContent("«Au secours !» des civils crient et cours dans tous les sens sur " . $planetAtt->getName() . " en <span><a href='/connect/carte-spatiale/" . $planetAtt->getSector()->getPosition() . "/" . $planetAtt->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $planetAtt->getSector()->getGalaxy()->getPosition() . ":" . $planetAtt->getSector()->getPosition() . ":" . $planetAtt->getPosition() . "</a></span>.<br>Vous n'aviez pas prévu suffisament de soldats et tanks pour faire face a la menace et des zombies envahissent les villes. Heureusement pour vous les travailleurs se réunissent et parviennent exterminer les zombies mais ce n'est pas grâce a vous.<br>" . number_format($soldierAtmp) . " zombies sont tués. <span class='text-rouge'>" . number_format($soldierDtmp) . "</span> de vos soldats succombent aux mâchoires de ces infamies et <span class='text-rouge'>" . number_format($tankDtmp) . "</span> tanks sont mit hors de service. <span class='text-rouge'>" . number_format($workerDtmp) . "</span> de vos travailleurs sont retrouvés morts.<br>Vous ne remportez aucun points de Guerre pour avoir sacrifié vos civils.");
                            $em->persist($reportDef);
                        } else {
                            $diviser = (1 + ($zCommander->getPoliticTankDef() / 10)) * 3000;
                            $planetAtt->setTank(round($aMilitary / $diviser));
                            $tankDtmp = $tankDtmp - $planetAtt->getTank();
                            $reportDef->setContent("Vos tanks ont suffit a arrêter les zombies pour cette fois-ci sur la planète " . $planetAtt->getName() . " en <span><a href='/connect/carte-spatiale/" . $planetAtt->getSector()->getPosition() . "/" . $planetAtt->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $planetAtt->getSector()->getGalaxy()->getPosition() . ":" . $planetAtt->getSector()->getPosition() . ":" . $planetAtt->getPosition() . "</a></span>.br>Mais pensez a rester sur vos gardes. Votre armée extermine " . number_format($soldierAtmp) . " zombies. <span class='text-rouge'>" . number_format($soldierDtmp) . "</span> de vos soldats succombent aux mâchoires de ces infamies et <span class='text-rouge'>" . number_format($tankDtmp) . "</span> tanks sont mit hors de service.<br>Vous remportez <span class='text-vert'>+" . number_format($warPointDef) . "</span> points de Guerre.");
                            $em->persist($reportDef);
                        }
                    } else {
                        $diviser = (1 + ($zCommander->getPoliticSoldierAtt() / 10)) * (6 * $zCommander->getBarbedAdv());
                        $planetAtt->setSoldier(round($aMilitary / $diviser));
                        $soldierDtmp = $soldierDtmp - $planetAtt->getSoldier();
                        $reportDef->setContent("Une attaque de zombie est déclarée sur " . $planetAtt->getName() . " en <span><a href='/connect/carte-spatiale/" . $planetAtt->getSector()->getPosition() . "/" . $planetAtt->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $planetAtt->getSector()->getGalaxy()->getPosition() . ":" . $planetAtt->getSector()->getPosition() . ":" . $planetAtt->getPosition() . "</a></span>.<br>Vous étiez préparé a cette éventualité et vos soldats exterminent " . number_format($soldierAtmp) . " zombies. <span class='text-rouge'>" . number_format($soldierDtmp) . "</span> de vos soldats succombent aux mâchoires de ces infamies.<br>Vous remportez <span class='text-vert'>+" . number_format($warPointDef) . "</span> points de Guerre.");
                        $em->persist($reportDef);
                    }
                } else {
                    $zCommander->setZombieLvl((round($zCommander->getZombieLvl() / 2)));
                    $soldierDtmp = $planetAtt->getSoldier() != 0 ? $planetAtt->getSoldier() : 1;
                    $workerDtmp = $planetAtt->getWorker();
                    $tankDtmp = $planetAtt->getTank();
                    $reportDef->setTitle("Rapport invasion zombies : Défaite");
                    $reportDef->setType("zombie");
                    $reportDef->setImageName("zombie_lose_report.webp");
                    $reportDef->setContent("Vous recevez des rapports de toutes parts vous signalant des zombies sur la planète " . $planetAtt->getName() . " en <span><a href='/connect/carte-spatiale/" . $planetAtt->getSector()->getPosition() . "/" . $planetAtt->getSector()->getGalaxy()->getPosition() . "/" . $usePlanet->getId() . "'>" . $planetAtt->getSector()->getGalaxy()->getPosition() . ":" . $planetAtt->getSector()->getPosition() . ":" . $planetAtt->getPosition() . "</a></span>.<br>Mais vous tardez a réagir et le manque de préparation lui est fatale.<br>Vous recevez ces derniers mots de votre Gouverneur local «Salopard! Vous étiez censé nous protéger, ou est l'armée !»<br> Les dernières images de la planète vous montre des zombies envahissant le moindre recoin de la planète.<br>Vos <span class='text-rouge'>" . number_format($soldierDtmp) . "</span> soldats, <span class='text-rouge'>" . number_format($tankDtmp) . "</span> tanks et <span class='text-rouge'>" . number_format($workerDtmp) . "</span> travailleurs sont tous mort. Votre empire en a pris un coup, mais il vous reste des planètes, remettez vous en question! Consolidez vos positions et allez détuire les nids de zombies!");
                    $em->persist($reportDef);
                    $planetAtt->setWorker(125000);
                    if ($planetAtt->getGround() != 25 && $planetAtt->getSky() != 5) {
                        if ($planetAtt->getSoldierMax() >= 2500) {
                            $planetAtt->setSoldier($planetAtt->getSoldierMax());
                        } else {
                            $planetAtt->setCaserne(1);
                            $planetAtt->setSoldier(500);
                            $planetAtt->setSoldierMax(500);
                        }
                        $planetAtt->setName('Base Zombie');
                        $planetAtt->setImageName('hydra_planet.webp');
                        $planetAtt->setCommander($zombie);
                    } else {
                        $planetAtt->setName('Inhabitée');
                        $planetAtt->setCommander(null);
                    }
                    $em->flush();
                    if ($zCommander->getAllPlanets() == 0) {
                        $zCommander->setGameOver($zombie->getUsername());
                        $zCommander->setGrade(null);
                        foreach ($zCommander->getFleets() as $tmpFleet) {
                            $tmpFleet->setCommander($zombie);
                            $tmpFleet->setFleetList(null);
                        }
                    }
                }
            } else {
                if ($zCommander->getAlliance()) {
                    if ($zCommander->getAlliance()->getPolitic() == 'fascism') {
                        $zCommander->setZombieLvl($zCommander->getZombieLvl() + 150);
                    } else {
                        $zCommander->setZombieLvl($zCommander->getZombieLvl() + 200);
                    }
                } else {
                    $zCommander->setZombieLvl($zCommander->getZombieLvl() + 100);
                }
            }
            $zCommander->setNewReport(false);
            $timeAtt = new DateTime();
            $timeAtt->add(new DateInterval('PT' . round(86400 * rand(1,3)) . 'S'));
            $nextZombie = new DateTime();
            $nextZombie->add(new DateInterval('PT' . round(12 * rand(1,5)) . 'H'));
            $zCommander->setZombieAt($nextZombie);
            $fleetZb = new Fleet();
            $fleetZb->setName('Horde');
            $fleetZb->setHunter(1 + round(($zCommander->getAllShipPoint() / (3 * rand(1, 5))) / 5));
            $fleetZb->setHunterWar(1 + round(($zCommander->getAllShipPoint() / (4 * rand(1, 5))) / 5));
            $fleetZb->setCorvet(1 + round(($zCommander->getAllShipPoint() / (5 * rand(1, 5))) / 5));
            $fleetZb->setCorvetLaser(1 + round(($zCommander->getAllShipPoint() / (6 * rand(1, 5))) / 5));
            $fleetZb->setCorvetWar(1 + round(($zCommander->getAllShipPoint() / (7 * rand(1, 5))) / 5));
            $fleetZb->setFregate(1 + round(($zCommander->getAllShipPoint() / (8 * rand(1, 5))) / 5));
            $fleetZb->setCommander($zombie);
            $fleetZb->setPlanet($planetZb);
            $fleetZb->setSignature($fleetZb->getNbSignature());
            $destination = new Destination($fleetZb, $planetAtt);
            $em->persist($destination);
            $fleetZb->setFlightTime($timeAtt);
            $fleetZb->setAttack(1);
            $fleetZb->setFlightAt(1);
            $em->persist($fleetZb);
        }
        echo "Flush -> " . count($zCommanders) . " ";

        $em->flush();

        return new Response ("<span style='color:#008000'>OK</span><br/>");
    }
}