<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Report;
use App\Entity\Fleet;
use App\Entity\Planet;
use App\Entity\Exchange;
use DateTime;
use DateTimeZone;

class FightController extends AbstractController
{
    /**
     * @Route("/clash/", name="fight_war_area")
     */
    public function fightAction()
    {
        $em = $this->getDoctrine()->getManager();

        while(1) {
            $winner = null;
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));

            $firstFleet = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.planet', 'p')
                ->select('p.id')
                ->where('f.fightAt < :now')
                ->andWhere('f.flightTime is null')
                ->setParameters(['now' => $now])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            if (!$firstFleet) {
                exit;
            }

            $fleetsWars = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.planet', 'p')
                ->where('p.id = :id')
                ->andWhere('f.flightTime is null')
                ->setParameters(['id' => $firstFleet['id']])
                ->orderBy('f.attack', 'ASC')
                ->getQuery()
                ->getResult();

            $teamBlock = [];
            $fleetsId = [];
            foreach ($fleetsWars as $fleetsWar) {
                if ($fleetsWar->getUser()->getAlly()) {
                    if (in_array($fleetsWar->getUser()->getAlly()->getSigle(), $teamBlock) == false && $fleetsWar->getUser()->getAlly()->getSigleAlliedArray($teamBlock) == NULL &&
                        in_array($fleetsWar->getId(), $fleetsId) == false) {
                        $teamBlock[] = $fleetsWar->getUser()->getAlly()->getSigle();
                        $fleetsId[] = $fleetsWar->getId();
                    }
                } elseif (in_array($fleetsWar->getUser()->getUserName(), $teamBlock) == false &&
                    in_array($fleetsWar->getId(), $fleetsId) == false) {
                    $teamBlock[] = $fleetsWar->getUser()->getUserName();
                    $fleetsId[] = $fleetsWar->getId();
                }
            }
            $tmpcount = count($teamBlock);
            if ($tmpcount < 2) {
                foreach ($fleetsWars as $fleetsWar) {
                    $fleetsWar->setFightAt(null);
                    $em->flush();
                }
                exit;
            }
            $team = $tmpcount;
            $isAttack = [];

            while ($team > 0) {
                $team--;
                ${'oneBlock' . $team} = new \ArrayObject();
                foreach ($fleetsWars as $fleetsWar) {
                    if ($fleetsWar->getUser()->getAlly()) {
                        if ($teamBlock[$team] == $fleetsWar->getUser()->getAlly()->getSigle() || $fleetsWar->getUser()->getAlly()->getSigleAllied($teamBlock[$team])) {
                            ${'oneBlock' . $team}->append($fleetsWar);
                            $isAttack[$team] = $fleetsWar->getAttack();
                        }
                    } elseif ($teamBlock[$team] == $fleetsWar->getUser()->getUserName()) {
                        ${'oneBlock' . $team}->append($fleetsWar);
                        $isAttack[$team] = $fleetsWar->getAttack();
                    }
                }
            }
            $team1 = $tmpcount - 1;
            $team2 = $tmpcount - 2;

            if ($isAttack[$team1] == true || $isAttack[$team2] == true) {
                $winner = self::attackAction(${'oneBlock' . $team1}, ${'oneBlock' . $team2});
            } else {
                while ($isAttack[$team2--] == true) {
                    $winner = self::attackAction(${'oneBlock' . $team1}, ${'oneBlock' . $team2});
                }
            }
            if ($winner != null) {
                if ($winner == ${'oneBlock' . $team1}) {
                    $team2 = $team2 - 1;
                    ${'oneBlock' . $team1} = $winner;
                } elseif ($winner == ${'oneBlock' . $team2}) {
                    $team1 = $team1 - $team2;
                    ${'oneBlock' . $team2} = $winner;
                }
            } else {
                foreach ($fleetsWars as $fleetsWar) {
                    $fleetsWar->setFightAt(null);
                    $em->flush();
                }
                exit;
            }

            $team = $tmpcount - 2;
            while ($team > 0) {
                $winner = self::attackAction(${'oneBlock' . $team1}, ${'oneBlock' . $team2});
                $team--;
                if ($winner == ${'oneBlock' . $team1}) {
                    $team2 = $team2 - 1;
                    ${'oneBlock' . $team1} = $winner;
                } elseif ($winner == ${'oneBlock' . $team2}) {
                    $team1 = $team1 - $team2;
                    ${'oneBlock' . $team2} = $winner;
                }
            }
            $em->flush();
        }
        exit;
      }


    public function attackAction($blockAtt, $blockDef)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $server->setNbrBattle($server->getNbrBattle() + 1);

        $armor = 0;
        $shield = 0;
        $missile = 0;
        $laser = 0;
        $plasma = 0;
        $debrisAtt = 0;
        $armorD = 0;
        $shieldD = 0;
        $missileD = 0;
        $laserD = 0;
        $plasmaD = 0;
        $debrisDef = 0;
        $politicA = null;
        $politicB = null;

        foreach($blockAtt as $attacker) {
            if ($attacker->getUser()->getAlly()) {
                $politicA = $attacker->getUser()->getAlly()->getPolitic();
                if ($attacker->getUser()->getPoliticArmor() > 0) {
                    $armor = $armor + ($attacker->getArmor() * (1 + ($attacker->getUser()->getPoliticArmor() / 10)));
                } else {
                    $armor = $armor + $attacker->getArmor();
                }
                if ($attacker->getUser()->getPoliticArmement() > 0) {
                    $missile = $missile + ($attacker->getMissile() * (1 + ($attacker->getUser()->getPoliticArmement() / 10)));
                    $laser = $laser + ($attacker->getLaser() * (1 + ($attacker->getUser()->getPoliticArmement() / 10)));
                } else {
                    $missile = $missile + $attacker->getMissile();
                    $laser = $laser + $attacker->getLaser();
                }
            } else {
                $armor = $armor + $attacker->getArmor();
                $missile = $missile + $attacker->getMissile();
                $laser = $laser + $attacker->getLaser();
            }
            $plasma = $plasma + $attacker->getPlasma();
            $shield = $shield + $attacker->getShield();
            $debrisAtt = $debrisAtt + $attacker->getNbrSignatures() + $attacker->getCargoFull();
            $armeSaveA = $missile + $laser + $plasma;
        }
        foreach($blockDef as $defender) {
            if ($defender->getUser()->getAlly()) {
                $politicB = $defender->getUser()->getAlly()->getPolitic();
                if ($defender->getUser()->getPoliticArmor() > 0) {
                    $armorD = $armorD + ($defender->getArmor() * (1 + ($defender->getUser()->getPoliticArmor() / 10)));
                } else {
                    $armorD = $armorD + $defender->getArmor();
                }
                if ($defender->getUser()->getPoliticArmement() > 0) {
                    $missileD = $missileD + ($defender->getUser()->getMissile() * (1 + ($defender->getUser()->getPoliticArmement() / 10)));
                    $laserD = $laserD + ($defender->getLaser() * (1 + ($defender->getUser()->getPoliticArmement() / 10)));
                } else {
                    $missileD = $missileD + $defender->getMissile();
                    $laserD = $laserD + $defender->getLaser();
                }
            } else {
                $armorD = $armorD + $defender->getArmor();
                $missileD = $missileD + $defender->getMissile();
                $laserD = $laserD + $defender->getLaser();
            }
            $shieldD = $shieldD + $defender->getShield();
            $plasmaD = $plasmaD + $defender->getPlasma();
            $debrisDef = $debrisDef + $defender->getNbrSignatures() + $defender->getCargoFull();
            $armeSaveB = $laserD + $plasmaD + $missileD;
        }
        if ($politicA && $politicB && $politicA != $politicB) {
            if ($politicA == 'fascim' && $politicB == 'communism') {
                $missileD = $missileD * 1.2;
                $laserD = $laserD * 1.2;
                $plasmaD = $plasmaD * 1.2;
            } elseif ($politicA == 'fascim' && $politicB == 'democrat') {
                $missile = $missile * 1.2;
                $laser = $laser * 1.2;
                $plasma = $plasma * 1.2;
            } elseif ($politicA == 'democrat' && $politicB == 'communism') {
                $missile = $missile * 1.2;
                $laser = $laser * 1.2;
                $plasma = $plasma * 1.2;
            } elseif ($politicA == 'democrat' && $politicB == 'fascim') {
                $missile = $missile * 1.2;
                $laser = $laser * 1.2;
                $plasma = $plasma * 1.2;
            } elseif ($politicA == 'communism' && $politicB == 'democrat') {
                $missile = $missile * 1.2;
                $laser = $laser * 1.2;
                $plasma = $plasma * 1.2;
            } elseif ($politicA == 'communism' && $politicB == 'fascim') {
                $missile = $missile * 1.2;
                $laser = $laser * 1.2;
                $plasma = $plasma * 1.2;
            }
        }

        $armorSaveA = $armor;
        $armorSaveD = $armorD;
        $warPointA = round($armor / 80);
        $warPointB = round($armorD / 80);
        $attAll = $missile + $laser + $plasma;
        $defAll = $missileD + $laserD + $plasmaD;

        if($attAll <= 0 && $defAll <= 0) {
            foreach($blockDef as $cancelAtt) {
                $cancelAtt->setAttack(0);
            }
            foreach($blockAtt as $cancelAtt) {
                $cancelAtt->setAttack(0);
            }
            $em->flush();
            return($blockAtt);
        }

        if($attAll > 0 && $defAll <= 0) {
            foreach($blockDef as $removeOne) {
                $reportLoseUtilA = new Report();
                $reportLoseUtilA->setType('fight');
                $reportLoseUtilA->setSendAt($now);
                $reportLoseUtilA->setImageName("f_lose_report.jpg");
                $reportLoseUtilA->setContent("Votre flotte utilitaire " . $removeOne->getName() . " ne dispose pas des technologies nécessaires à l'identification des vaisseaux ennemis en " . $removeOne->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $removeOne->getPlanet()->getSector()->getPosition() . ":" . $removeOne->getPlanet()->getPosition() . " .");
                $reportLoseUtilA->setTitle("Rapport de combat : Défaite");
                $reportLoseUtilA->setUser($removeOne->getUser());
                $removeOne->getUser()->setViewReport(false);
                $em->persist($reportLoseUtilA);
                $em->remove($removeOne);
            }
            foreach($blockAtt as $reportWin) {
                $reportWinUtilA = new Report();
                $reportWinUtilA->setType('fight');
                $reportWinUtilA->setSendAt($now);
                $reportWinUtilA->setImageName("f_win_report.jpg");
                $reportWinUtilA->setContent("Vous venez de détruire une flotte utilitaire en " . $reportWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $reportWin->getPlanet()->getSector()->getPosition() . ":" . $reportWin->getPlanet()->getPosition() . " .");
                $reportWinUtilA->setTitle("Rapport de combat : Victoire");
                $reportWin->getUser()->setViewReport(false);
                $reportWinUtilA->setUser($reportWin->getUser());
                $em->persist($reportWinUtilA);
                $planet = $reportWin->getPlanet();
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisDef * rand(30,40)));
            $planet->setWtCdr($planet->getWtCdr() + $debrisDef * rand(20,30));
            $em->flush();
            return($blockAtt);
        }
        if($defAll > 0 && $attAll <= 0) {
            foreach($blockAtt as $removeTwo) {
                $reportLoseUtilB = new Report();
                $reportLoseUtilB->setType('fight');
                $reportLoseUtilB->setSendAt($now);
                $reportLoseUtilB->setImageName("f_lose_report.jpg");
                $reportLoseUtilB->setContent("Votre flotte utilitaire " . $removeTwo->getName() . " ne dispose pas des technologies nécessaires à l'identification des vaisseaux ennemis " . $removeTwo->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $removeTwo->getPlanet()->getSector()->getPosition() . ":" . $removeTwo->getPlanet()->getPosition() . " .");
                $reportLoseUtilB->setTitle("Rapport de combat : Défaite");
                $reportLoseUtilB->setUser($removeTwo->getUser());
                $removeTwo->getUser()->setViewReport(false);
                $em->persist($reportLoseUtilB);
                $em->remove($removeTwo);
            }
            foreach($blockDef as $reportWin) {
                $reportWinUtilB = new Report();
                $reportWinUtilB->setType('fight');
                $reportWinUtilB->setSendAt($now);
                $reportWinUtilB->setImageName("f_win_report.jpg");
                $reportWinUtilB->setContent("Vous venez de détruire une flotte utilitaire en " . $reportWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $reportWin->getPlanet()->getSector()->getPosition() . ":" . $reportWin->getPlanet()->getPosition() . " .");
                $reportWinUtilB->setTitle("Rapport de combat : Victoire");
                $reportWinUtilB->setUser($reportWin->getUser());
                $reportWin->getUser()->setViewReport(false);
                $em->persist($reportWinUtilB);
                $planet = $reportWin->getPlanet();
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisAtt * rand(30,40)));
            $planet->setWtCdr($planet->getWtCdr() + $debrisAtt * rand(20,30));
            $em->flush();
            return($blockDef);
        }

        $firstBlood = (($missile / 4) + ($plasma * 1.5) + ($laser));
        $firstBloodD = (($missileD / 4) + ($plasmaD * 1.5) + ($laserD));
        $countSAtt = 0;
        $countSDef = 0;
        if(($firstBlood > 0) && $shieldD > 0) {
            while ($shieldD > 0) {
                $countSAtt++;
                $shieldD = $shieldD - $firstBlood;
            }
            $armorD = $armorD - $firstBlood;
        } elseif ($shieldD < 0) {
            $countSAtt = 1;
            $armorD = $armorD - $firstBlood;
        }
        if(($firstBloodD > 0) && $shield > 0) {
            while($shield > 0) {
                $countSDef++;
                $shield = $shield - $firstBloodD;
            }
            $armor = $armor - $firstBloodD;
        } elseif ($shieldD < 0) {
            $countSAtt = 1;
            $armor = $armor - $firstBloodD;
        }
        $secondShot = ($missile + ($plasma / 1.5) + $laser);
        $secondShotD = ($missileD + ($plasmaD / 1.5) + $laserD);
        if($countSDef - $countSAtt > 0) {
            $armorD = $armorD - ($secondShot * ($countSDef - $countSAtt));
            $secondShotD = ($missileD + ($plasmaD / 1.5) + $laserD);
        }
        if($countSAtt - $countSDef > 0) {
            $armor = $armor - ($secondShot * ($countSAtt - $countSDef));
            $secondShot = ($missile + ($plasma / 1.5) + $laser);
        }

        $countShot = 0;
        while($armorD > 0 && $armor > 0 && $countShot < 100) {
            $countShot++;
            if ($armorD > 0) {
                $armorD = $armorD - $secondShot;
                $tmpSecondShot = $secondShot;
                $secondShot = $secondShot;
            }
            if($armor > 0) {
                $armor = $armor - $tmpSecondShot;
                $secondShotD = $secondShotD;
            }
        }

        if ($armorD > $armor || $shieldD > 0) {
            if($armorD * 1.1 < $armorSaveD) {
                $armorD = $armorD * (rand(11, 13) / 10);
            }
            if($armorD < 0) {
                $armorD = $armorSaveD / 20;
            }
            foreach($blockDef as $defenderWin) {
                $reportWinA = new Report();
                $reportWinA->setType('fight');
                $reportWinA->setSendAt($now);
                $reportWinA->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSDef . " tir(s) pour percer les boucliers</th></tr>");
                $reportWinA->setTitle("Rapport de combat : Victoire");
                $reportWinA->setUser($defenderWin->getUser());
                foreach ($blockDef as $fleetA) {
                    $player = $fleetA->getFleetTags();
                    if($armorSaveD != $armorD) {
                        $percentArmor = ($fleetA->getArmor() * 100) / $armorSaveD;
                        $newArmor = round($fleetA->getArmor() - (round($percentArmor * $armorD) / 100));
                        $ships = $fleetA->getShipsReport($newArmor);
                    } else {
                        $ships = $fleetA->getShipsReportNoLost();
                    }
                    $reportWinA->setContent($reportWinA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $ships . "</th></tr>");
                }
                $reportWinA->setContent($reportWinA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 2</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                foreach ($blockAtt as $fleetB) {
                    $player = $fleetB->getFleetTags();
                    $lose = $fleetB->getShipsLoseReport();
                    $reportWinA->setContent($reportWinA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $lose . "</th></tr>");
                }
                $reportWinA->setContent($reportWinA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $countShot . " rounds de combat.</th></tr></tbody></table>");
                $reportWinA->setContent($reportWinA->getContent() . "Vous avez gagné le combat en "  . $defenderWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderWin->getPlanet()->getSector()->getPosition() . ":" . $defenderWin->getPlanet()->getPosition() . " , vous remportez " . number_format($warPointA * 10) . " points de Guerre");
                $reportWinA->setImageName("fight_win_report.jpg");
                $defenderWin->getUser()->setViewReport(false);
                $quest = $defenderWin->getUser()->checkQuests('destroy_fleet');
                if($quest) {
                    $defenderWin->getUser()->getRank()->setWarPoint($defenderWin->getUser()->getRank()->getWarPoint() + $quest->getGain());
                    $defenderWin->getUser()->removeQuest($quest);
                }
                $em->persist($reportWinA);
            }
            foreach($blockAtt as $attackerLose) {
                $reportLoseA = new Report();
                $reportLoseA->setType('fight');
                $reportLoseA->setSendAt($now);
                $reportLoseA->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                $reportLoseA->setTitle("Rapport de combat : Défaite");
                $reportLoseA->setImageName("fight_lose_report.jpg");
                $reportLoseA->setUser($attackerLose->getUser());
                foreach ($blockAtt as $fleetB) {
                    $player = $fleetB->getFleetTags();
                    $lose = $fleetB->getShipsLoseReport();
                    $reportLoseA->setContent($reportLoseA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $lose . "</th></tr>");
                }
                $reportLoseA->setContent($reportLoseA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 2</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSDef . " tir(s) pour percer les boucliers</th></tr>");
                foreach ($blockDef as $fleetA) {
                    $player = $fleetA->getFleetTags();
                    if($armorSaveD != $armorD) {
                        $percentArmor = ($fleetA->getArmor() * 100) / $armorSaveD;
                        $newArmor = $fleetA->getArmor() - (round($percentArmor * $armorD) / 100);
                        $ships = $fleetA->getShipsReport($newArmor);
                    } else {
                        $ships = $fleetA->getShipsReportNoLost();
                    }
                    $reportLoseA->setContent($reportLoseA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $ships . "</th></tr>");
                }
                $reportLoseA->setContent($reportLoseA->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $countShot . " rounds de combat.</th></tr></tbody></table>");
                $reportLoseA->setContent($reportLoseA->getContent() . "Vous avez perdu le combat en " . $attackerLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerLose->getPlanet()->getSector()->getPosition() . ":" . $attackerLose->getPlanet()->getPosition() . " , vos adversaires remportent " . number_format($warPointA * 10) . " points de Guerre.");
                $attackerLose->getUser()->setViewReport(false);
                $planet = $attackerLose->getPlanet();

                $loseArm = $attackerLose->getLaser() + $attackerLose->getMissile() + $attackerLose->getPlasma();
                $percentWarPoint = ($loseArm * 100) / $armeSaveA;
                $warPointB = ($warPointB - ($armorD / 80)) / 10;
                $newWarPoint = round(($percentWarPoint * $warPointB) / 10);
                if ($attackerLose->getUser()->getPoliticPdg() > 0) {
                    $newWarPoint = round($newWarPoint * (1 + ($attackerLose->getUser()->getPoliticPdg() / 10)));
                }
                if($newWarPoint < 0) {
                    $newWarPoint = $newWarPoint * -1;
                }
                if($attackerLose->getUser()->getPeaces()) {
                    $peace = $attackerLose->getUser()->getPeaces();
                    if ($peace->getPdg() > 0) {
                        $pdgPeace = round($newWarPoint * ($peace->getPdg() / 100));
                        $newWarPoint = $newWarPoint - $pdgPeace;
                        $otherAlly = $em->getRepository('App:Ally')
                            ->createQueryBuilder('a')
                            ->where('a.sigle = :sigle')
                            ->setParameter('sigle', $peace->getAllyTag())
                            ->getQuery()
                            ->getOneOrNullResult();

                        $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                        $exchangeLoseA = new Exchange();
                        $exchangeLoseA->setAlly($otherAlly);
                        $exchangeLoseA->setCreatedAt($now);
                        $exchangeLoseA->setType(1);
                        $exchangeLoseA->setAmount($pdgPeace);
                        $exchangeLoseA->setAccepted(1);
                        $exchangeLoseA->setContent("Taxe liée à la paix.");
                        $exchangeLoseA->setName($attackerLose->getUser()->getUserName());
                        $em->persist($exchangeLoseA);
                        $reportLoseA->setContent($reportLoseA->getContent() . " Votre accord de paix ayant envoyé " . number_format($pdgPeace) . " points de Guerre à l'alliance [" . $otherAlly->getSigle() . "].");
                    }
                }
                $reportLoseA->setContent($reportLoseA->getContent() . " Mais vous remportez vous même " . number_format($newWarPoint) . " points de Guerre !");
                if($attackerLose->getUser()->getRank()) {
                    $attackerLose->getUser()->getRank()->setWarPoint($attackerLose->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $em->persist($reportLoseA);
                $em->remove($attackerLose);
            }
            foreach($blockDef as $defenderWin) {
                $defArm = $defenderWin->getLaser() + $defenderWin->getMissile() + $defenderWin->getPlasma();
                $percentWarPoint = ($defArm * 100) / $armeSaveB;
                $newWarPoint = round(($percentWarPoint * $warPointA) / 10);
                if ($defenderWin->getUser()->getPoliticPdg() > 0) {
                    $newWarPoint = round($newWarPoint * (1 + ($defenderWin->getUser()->getPoliticPdg() / 10)));
                }
                if($defenderWin->getUser()->getPeaces()) {
                    $peace = $defenderWin->getUser()->getPeaces();
                    if ($peace->getPdg() > 0) {
                        $pdgPeace = round($newWarPoint * ($peace->getPdg() / 100));
                        $newWarPoint = $newWarPoint - $pdgPeace;
                        $otherAlly = $em->getRepository('App:Ally')
                            ->createQueryBuilder('a')
                            ->where('a.sigle = :sigle')
                            ->setParameter('sigle', $peace->getAllyTag())
                            ->getQuery()
                            ->getOneOrNullResult();
                        $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                        $exchangeWinA = new Exchange();
                        $exchangeWinA->setAlly($otherAlly);
                        $exchangeWinA->setCreatedAt($now);
                        $exchangeWinA->setType(1);
                        $exchangeWinA->setAmount($pdgPeace);
                        $exchangeWinA->setAccepted(1);
                        $exchangeWinA->setContent("Taxe liée à la paix.");
                        $exchangeWinA->setName($defenderWin->getUser()->getUserName());
                        $em->persist($exchangeWinA);
                    }
                }
                $percentArmor = ($defenderWin->getArmor() * 100) / $armorSaveD;
                $newArmor = $defenderWin->getArmor() - (round($percentArmor * $armorD) / 100);
                $defenderWin->setFleetWinRatio($newArmor);
                if($defenderWin->getUser()->getRank()) {
                    $defenderWin->getUser()->getRank()->setWarPoint($defenderWin->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $defenderWin->setFightAt(null);
                $em->flush();
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisAtt * rand(30,40)));
            $planet->setWtCdr($planet->getWtCdr() + $debrisAtt * rand(20,30));
            $em->flush();
            return($blockDef);
        } else {
            if($armor * 1.1 < $armorSaveA) {
                $armor = $armor * (rand(11, 13) / 10);
            }
            if($armor < 0) {
                $armor = $armorSaveA / 20;
            }
            foreach($blockAtt as $attackerWin) {
                $reportWinB = new Report();
                $reportWinB->setType('fight');
                $reportWinB->setSendAt($now);
                $reportWinB->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSDef . " tir(s) pour percer les boucliers</th></tr>");
                $reportWinB->setTitle("Rapport de combat : Victoire");
                $reportWinB->setImageName("fight_win_report.jpg");
                $reportWinB->setUser($attackerWin->getUser());
                foreach ($blockDef as $fleetA) {
                    $player = $fleetA->getFleetTags();
                    $lose = $fleetA->getShipsLoseReport();
                    $reportWinB->setContent($reportWinB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $lose . "</th></tr>");
                }
                $reportWinB->setContent($reportWinB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 2</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                foreach ($blockAtt as $fleetB) {
                    $player = $fleetB->getFleetTags();
                    if($armorSaveA != $armor) {
                        $percentArmor = ($fleetB->getArmor() * 100) / $armorSaveA;
                        $newArmor = $fleetB->getArmor() - (round($percentArmor * $armor) / 100);
                        $ships = $fleetB->getShipsReport($newArmor);
                    } else {
                        $ships = $fleetB->getShipsReportNoLost();
                    }
                    $reportWinB->setContent($reportWinB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $ships . "</th></tr>");
                }
                $reportWinB->setContent($reportWinB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $countShot . " rounds de combat.</th></tr></tbody></table>");
                $reportWinB->setContent($reportWinB->getContent() . "Vous avez gagné le combat en "  . $attackerWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerWin->getPlanet()->getSector()->getPosition() . ":" . $attackerWin->getPlanet()->getPosition() . " , vous remportez " . number_format($warPointB * 10) . " points de Guerre");
                $attackerWin->getUser()->setViewReport(false);
                $em->persist($reportWinB);
                $quest = $attackerWin->getUser()->checkQuests('destroy_fleet');
                if($quest) {
                    $attackerWin->getUser()->getRank()->setWarPoint($attackerWin->getUser()->getRank()->getWarPoint() + $quest->getGain());
                    $attackerWin->getUser()->removeQuest($quest);
                }
            }
            foreach($blockDef as $defenderLose) {
                $reportLoseB = new Report();
                $reportLoseB->setType('fight');
                $reportLoseB->setSendAt($now);
                $reportLoseB->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                $reportLoseB->setTitle("Rapport de combat : Défaite");
                $reportLoseB->setImageName("fight_lose_report.jpg");
                $reportLoseB->setUser($defenderLose->getUser());
                foreach ($blockAtt as $fleetB) {
                    $player = $fleetB->getFleetTags();
                    if($armorSaveA != $armor) {
                        $percentArmor = ($fleetB->getArmor() * 100) / $armorSaveA;
                        $newArmor = $fleetB->getArmor() - (round($percentArmor * $armor) / 100);
                        $ships = $fleetB->getShipsReport($newArmor);
                    } else {
                        $ships = $fleetB->getShipsReportNoLost();
                    }
                    $reportLoseB->setContent($reportLoseB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $ships . "</th></tr>");
                }
                $reportLoseB->setContent($reportLoseB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 2</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSDef . " tir(s) pour percer les boucliers</th></tr>");
                foreach ($blockDef as $fleetA) {
                    $player = $fleetA->getFleetTags();
                    $lose = $fleetA->getShipsLoseReport();
                    $reportLoseB->setContent($reportLoseB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $player . "</th><th class=\"tab-cells-name p-1 ml-2\">" . $lose . "</th></tr>");
                }
                $reportLoseB->setContent($reportLoseB->getContent() . "<tr><th class=\"tab-cells-name p-1 ml-2\">" . $countShot . " rounds de combat.</th></tr></tbody></table>");
                $reportLoseB->setContent($reportLoseB->getContent() . "Vous avez perdu le combat en " . $defenderLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderLose->getPlanet()->getSector()->getPosition() . ":" . $defenderLose->getPlanet()->getPosition() . " , vos adversaires remportent " . number_format($warPointB * 10) . " points de Guerre.");
                $defenderLose->getUser()->setViewReport(false);
                $planet = $defenderLose->getPlanet();
                $loseArm = $defenderLose->getLaser() + $defenderLose->getMissile() + $defenderLose->getPlasma();
                $percentWarPoint = ($loseArm * 100) / $armeSaveB;
                $warPointA = ($warPointA - ($armor / 80)) / 10;
                $newWarPoint = round(($percentWarPoint * $warPointA) / 10);
                if ($defenderLose->getUser()->getPoliticPdg() > 0) {
                    $newWarPoint = round($newWarPoint * (1 + ($defenderLose->getUser()->getPoliticPdg() / 10)));
                }
                if($newWarPoint < 0) {
                    $newWarPoint = $newWarPoint * -1;
                }
                if($defenderLose->getUser()->getPeaces()) {
                    $peace = $defenderLose->getUser()->getPeaces();
                    if ($peace->getPdg() > 0) {
                        $pdgPeace = round($newWarPoint * ($peace->getPdg() / 100));
                        $newWarPoint = $newWarPoint - $pdgPeace;
                        $otherAlly = $em->getRepository('App:Ally')
                            ->createQueryBuilder('a')
                            ->where('a.sigle = :sigle')
                            ->setParameter('sigle', $peace->getAllyTag())
                            ->getQuery()
                            ->getOneOrNullResult();
                        $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                        $exchangeLoseB = new Exchange();
                        $exchangeLoseB->setAlly($otherAlly);
                        $exchangeLoseB->setCreatedAt($now);
                        $exchangeLoseB->setType(1);
                        $exchangeLoseB->setAmount($pdgPeace);
                        $exchangeLoseB->setAccepted(1);
                        $exchangeLoseB->setContent("Taxe liée à la paix.");
                        $exchangeLoseB->setName($defenderLose->getUser()->getUserName());
                        $em->persist($exchangeLoseB);
                        $reportLoseB->setContent($reportWinB->getContent() . " Votre accord de paix ayant envoyé " . number_format($pdgPeace) . " points de Guerre à l'alliance [" . $otherAlly->getSigle() . "].");
                    }
                }
                $reportLoseB->setContent($reportWinB->getContent() . " Mais vous remportez vous même " . number_format($newWarPoint) . " points de Guerre !");
                if($defenderLose->getUser()->getRank()) {
                    $defenderLose->getUser()->getRank()->setWarPoint($defenderLose->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $em->persist($reportLoseB);
                $em->remove($defenderLose);
            }
            foreach($blockAtt as $attackerWin) {
                $attArm = $attackerWin->getLaser() + $attackerWin->getMissile() + $attackerWin->getPlasma();
                $percentWarPoint = ($attArm * 100) / $armeSaveA;
                $newWarPoint = round(($percentWarPoint * $warPointB) / 10);
                if ($attackerWin->getUser()->getPoliticPdg() > 0) {
                    $newWarPoint = round($newWarPoint * (1 + ($attackerWin->getUser()->getPoliticPdg() / 10)));
                }
                if($attackerWin->getUser()->getPeaces()) {
                    $peace = $attackerWin->getUser()->getPeaces();
                    if ($peace->getPdg() > 0) {
                        $pdgPeace = round($newWarPoint * ($peace->getPdg() / 100));
                        $newWarPoint = $newWarPoint - $pdgPeace;
                        $otherAlly = $em->getRepository('App:Ally')
                            ->createQueryBuilder('a')
                            ->where('a.sigle = :sigle')
                            ->setParameter('sigle', $peace->getAllyTag())
                            ->getQuery()
                            ->getOneOrNullResult();
                        $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                        $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                        $exchangeWinB = new Exchange();
                        $exchangeWinB->setAlly($otherAlly);
                        $exchangeWinB->setCreatedAt($now);
                        $exchangeWinB->setType(1);
                        $exchangeWinB->setAmount($pdgPeace);
                        $exchangeWinB->setAccepted(1);
                        $exchangeWinB->setContent("Taxe liée à la paix.");
                        $exchangeWinB->setName($attackerWin->getUser()->getUserName());
                        $em->persist($exchangeWinB);
                    }
                }
                $percentArmor = ($attackerWin->getArmor() * 100) / $armorSaveA;
                $newArmor = $attackerWin->getArmor() - (round($percentArmor * $armor) / 100);
                $attackerWin->setFleetWinRatio($newArmor);
                if($attackerWin->getUser()->getRank()) {
                    $attackerWin->getUser()->getRank()->setWarPoint($attackerWin->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $attackerWin->setFightAt(null);
                $em->persist($attackerWin);
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisDef * rand(30,40)));
            $planet->setWtCdr($planet->getWtCdr() + $debrisDef * rand(20,30));
            $em->flush();
            return($blockAtt);
        }
        exit;
    }

      /**
       * @Route("/hello-we-come-for-you/{invader}/{usePlanet}", name="invader_planet", requirements={"usePlanet"="\d+", "invader"="\d+"})
       */
    public function invaderAction(Planet $usePlanet, Fleet $invader)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        if ($usePlanet->getUser() != $user || $invader->getUser() != $user) {
            return $this->redirectToRoute('home');
        }
        if ($user->getPoliticBarge() > 0) {
            $barge = $invader->getBarge() * 2500 * (1 + ($user->getPoliticBarge() / 4));
        } else {
            $barge = $invader->getBarge() * 2500;
        }
        $defenser = $invader->getPlanet();
        $userDefender= $invader->getPlanet()->getUser();
        $barbed = $userDefender->getBarbedAdv();
        $dSoldier = $defenser->getSoldier() > 0 ? ($defenser->getSoldier() * 6) * $barbed : 0;
        $dTanks = $defenser->getTank() > 0 ? $defenser->getTank() * 900 : 0;
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
        if ($userDefender->getZombie() == 0) {
            $reportInv->setType('invade');
        } else {
            $reportInv->setType('zombie');
        }
        $reportInv->setSendAt($now);
        $reportInv->setUser($user);
        $user->setViewReport(false);

        if ($userDefender->getZombie() == 0) {
            $reportDef = new Report();
            $reportDef->setType('invade');
            $reportDef->setSendAt($now);
            $reportDef->setUser($userDefender);
        }
        $userDefender->setViewReport(false);
        $dSigle = null;
        if($userDefender->getAlly()) {
            $dSigle = $userDefender->getAlly()->getSigle();
        }

        if($barge && $invader->getPlanet()->getUser() && $invader->getAllianceUser() && $invader->getFightAt() == null && $invader->getFlightTime() == null && $user->getSigleAllied($dSigle) == NULL) {
            if($barge >= $invader->getSoldier()) {
                $aMilitary = $invader->getSoldier() * $alea;
                $soldierAtmp = $invader->getSoldier();
                $soldierAtmpTotal = 0;
            } else {
                $aMilitary = $barge * $alea;
                $soldierAtmp = $barge;
                $soldierAtmpTotal = $invader->getSoldier() - $barge;
            }
            if ($user->getPoliticSoldierAtt() > 0) {
                $aMilitary = $aMilitary * (1 + ($user->getPoliticSoldierAtt() / 10));
            }
            if($dMilitary > $aMilitary) {
                if ($userDefender->getZombie() == 0) {
                    $warPointDef = round($aMilitary);
                    if ($user->getPoliticPdg() > 0) {
                        $warPointDef = round($warPointDef * (1 + ($user->getPoliticPdg() / 10)));
                    }
                    $userDefender->getRank()->setWarPoint($userDefender->getRank()->getWarPoint() + $warPointDef);
                }
                $aMilitary = $dSoldier - $aMilitary;
                if($barge < $invader->getSoldier()) {
                    $invader->setSoldier($invader->getSoldier() - $barge);
                }
                $defenser->setBarge($defenser->getBarge() + $invader->getBarge());
                $invader->setBarge(0);
                if($aMilitary <= 0) {
                    $soldierDtmp = $defenser->getSoldier();
                    $workerDtmp = $defenser->getWorker();
                    $tankDtmp = $defenser->getTank();
                    $defenser->setSoldier(0);
                    $aMilitary = $dTanks - abs($aMilitary);
                    if($aMilitary <= 0) {
                        $defenser->setTank(0);
                        $defenser->setWorker($defenser->getWorker() + ($aMilitary / (1 + ($userDefender->getPoliticWorkerDef() / 5))));
                        $tankDtmp = $tankDtmp - $defenser->getTank();
                        $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                        $workerDtmp = $workerDtmp - $defenser->getWorker();
                    } else {
                        $diviser = (1 + ($userDefender->getPoliticTankDef() / 10)) * 900;
                        $defenser->setTank(round($aMilitary / $diviser));
                        $tankDtmp = $tankDtmp - $defenser->getTank();
                        $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                        $workerDtmp = $workerDtmp - $defenser->getWorker();
                    }
                } else {
                    $diviser = (1 + ($userDefender->getPoliticSoldierAtt() / 10)) * ($alea * $userDefender->getBarbedAdv());
                    $defenser->setSoldier($soldierAtmpTotal + round($aMilitary / $diviser));
                    $tankDtmp = $defenser->getTank();
                    $soldierDtmp = $soldierAtmpTotal + round($aMilitary / $diviser);
                    $workerDtmp = 0;
                }
                if ($userDefender->getZombie() == 1) {
                    $reportInv->setTitle("Rapport contre attaque : Défaite");
                    $reportInv->setImageName("zombie_lose_report.jpg");
                    $reportInv->setContent("Vous pensiez partir pour une promenade de santé mais la réalité vous rattrape vite... Vous avez envoyé tout vos soldats au casse-pipe.<br>Pire, vous avez attirer l'attention des zombies et fait monter la menace de 10 points ! Vous avez interêt a prendre vite " . $defenser->getName() . " en " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " sinon votre Empire ne tiendra pas longtemps. Vous avez tué <span class='text-vert'>" . number_format(round($soldierDtmp + ($workerDtmp / 6) + ($tankDtmp * 900))) . "</span> zombies. Tous vos soldats sont morts et vos barges se sont égarés sur la planète.<br>N'abandonnez pas et sortez vos tripes !");
                    $user->setZombieAtt($user->getZombieAtt() + 10);
                } else {
                    $reportDef->setTitle("Rapport d'invasion : Victoire (défense)");
                    $reportDef->setImageName("defend_win_report.jpg");
                    $reportDef->setContent("Bien joué ! Vos travailleurs et soldats ont repoussé l'invasion du joueur " . $defenser->getUser()->getUserName() . " sur votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  <span class='text-vert'>" . number_format($soldierAtmp) . "</span> soldats vous ont attaqué, tous ont été tué. Vous avez ainsi prit le contrôle des barges de l'attaquant.<br>Et vous remportez <span class='text-vert'>+" . number_format($warPointDef) . "</span> points de Guerre.");
                    $reportInv->setTitle("Rapport d'invasion : Défaite (attaque)");
                    $reportInv->setImageName("invade_lose_report.jpg");
                    $reportInv->setContent("'AH AH AH AH' le rire de " . $userDefender->getUserName() . " résonne à vos oreilles d'un curieuse façon. Votre sang bouillonne vous l'a vouliez cette planète. Qu'il rigole donc, vous reviendrez prendre " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " et ferez effacer des livres d'histoires son ridicule nom. Vous avez tout de même tué <span class='text-vert'>" . number_format($soldierDtmp) . "</span> soldats, <span class='text-vert'>" . number_format($tankDtmp) ."</span> tanks et <span class='text-vert'>" . number_format($workerDtmp) . "</span> travailleurs à l'ennemi. Tous vos soldats sont morts et vos barges sont resté sur la planète.<br>Courage commandant.");
                }
            } else {
                $soldierDtmp = $defenser->getSoldier() != 0 ? $defenser->getSoldier() : 1;
                $workerDtmp = $defenser->getWorker();
                $tankDtmp = $defenser->getTank();
                $warPointAtt = round(($soldierDtmp + ($workerDtmp / 10)) * 1);
                if ($user->getPoliticPdg() > 0) {
                    $warPointAtt = round($warPointAtt * (1 + ($user->getPoliticPdg() / 10)));
                }
                if ($user->getPoliticSoldierAtt() > 0) {
                    $aMilitary = $aMilitary / (1 + ($user->getPoliticSoldierAtt() / 10));
                }
                $invader->setSoldier($soldierAtmpTotal + round(($aMilitary - $dMilitary) / $alea));
                $soldierAtmp = $soldierAtmpTotal + $soldierAtmp - $invader->getSoldier();
                $defenser->setSoldier(0);
                $defenser->setTank(0);
                $defenser->setWorker(2000);
                if($invader->getUser()->getColPlanets() <= ($invader->getUser()->getTerraformation() + 1 + $user->getPoliticInvade()) && $userDefender->getZombie() == 0) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $warPointAtt);
                    $defenser->setUser($user);
                    $em->flush();
                    $reportDef->setTitle("Rapport d'invasion : Défaite (défense)");
                    $reportDef->setImageName("defend_lose_report.jpg");
                    $reportDef->setContent("Mais QUI ? QUI !!! Vous as donné un commandant si médiocre " . $defenser->getUser()->getUserName() . " n'a pas eu a faire grand chose pour prendre votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . number_format(round($soldierAtmp)) . " soldats ennemis sont tout de même éliminé. C'est toujours ça de gagner. Vos <span class='text-rouge'>-" . number_format($soldierDtmp) . "</span> soldats, <span class='text-rouge'>-" . number_format($tankDtmp) ."</span> tanks et <span class='text-rouge'>-" . number_format($workerDtmp) . "</span> travailleurs sont tous mort. Votre empire en a prit un coup, mais il vous reste des planètes, il est l'heure de la revanche !");
                    $reportInv->setTitle("Rapport d'invasion : Victoire (attaque)");
                    $reportInv->setImageName("invade_win_report.jpg");
                    $reportInv->setContent("Vous débarquez après que la planète ait été prise et vous installez sur le trône de " . $userDefender->getUserName() . ". Qu'il est bon d'entendre ses pleures lointains... La planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " est désormais votre! Il est temps de remettre de l'ordre dans la galaxie. <span class='text-rouge'>-" . number_format(round($soldierAtmp)) . "</span> de vos soldats ont péri dans l'invasion. Mais les défenseurs ont aussi leurs pertes : <span class='text-vert'>" . number_format($soldierDtmp) . "</span> soldats, <span class='text-vert'>" . number_format($tankDtmp) ."</span> tanks et <span class='text-vert'>" . number_format($workerDtmp) . "</span> travailleurs ont péri. Cependant vous épargnez 2000 travailleurs dans votre bonté (surtout pour faire tourner la planète).<br>Et vous remportez <span class='text-vert'>+" . number_format($warPointAtt) . "</span> points de Guerre.");
                } else {
                    $warPointAtt = $warPointAtt * 10;
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $warPointAtt);
                    $hydra = $em->getRepository('App:User')->findOneBy(['zombie' => 1]);
                    $reportInv->setTitle("Rapport contre attaque : Victoire");
                    $reportInv->setImageName("invade_win_report.jpg");
                    $reportInv->setContent("Vos soldats débarquent sur la planète zombie et sorte l'artillerie lourde ! Les rues s'enlisent de mort mais l'entraînement prévaut sur la peur et vous purgez cette planète de cette peste macabre.<br> La planète " . $defenser->getName() . " en " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " est désormais libre. Et votre indice d'attaque zombie est divisé par 10. Lors de l'assaut vous dénombrez <span class='text-rouge'>-" . number_format(round($soldierAtmp)) . "</span> pertes parmis vos soldats. Mais vous avez exterminé <span class='text-vert'>" . number_format(round($soldierDtmp + ($workerDtmp / 6) + ($tankDtmp * 900))) . "</span> zombies ! <br>Et vous remportez <span class='text-vert'>+" . number_format($warPointAtt) . "</span> points de Guerre.");

                    if ($userDefender->getZombie() == 1) {
                        $image = [
                            'planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png',
                            'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png',
                            'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png',
                            'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png',
                            'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png',
                            'planet31.png', 'planet32.png', 'planet33.png'
                        ];
                        $defenser->setUser(null);
                        $em->flush();
                        $user->setZombieAtt(round($user->getZombieAtt() / 10));
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
                if($userDefender->getColPlanets() == 0) {
                    $userDefender->setGameOver($user->getUserName());
                    $userDefender->setGrade(null);
                    foreach($userDefender->getFleets() as $tmpFleet) {
                        $tmpFleet->setUser($user);
                        $tmpFleet->setFleetList(null);
                    }
                }
                $quest = $user->checkQuests('invade');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            if($invader->getNbrShips() == 0) {
                $em->remove($invader);
            }
            $server->setNbrInvasion($server->getNbrInvasion() + 1);
            $em->persist($reportInv);
            if ($userDefender->getZombie() == 0) {
                $em->persist($reportDef);
            }
            $em->flush();
        }

        return $this->redirectToRoute('report', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/colonisation-planete/{fleet}/", name="colonizer_planet", requirements={"fleet"="\d+"})
     */
    public function colonizeAction(Fleet $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $colonize = $em->getRepository('App:Fleet')->find(['id' => $fleet]);
        $newPlanet = $colonize->getPlanet();

        if($colonize->getColonizer() && $newPlanet->getUser() == null &&
            $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
            $newPlanet->getCdr() == false && $colonize->getUser()->getColPlanets() < 26 &&
            $colonize->getUser()->getColPlanets() <= ($user->getTerraformation() + 1 + $user->getPoliticColonisation())) {

            $colonize->setColonizer($colonize->getColonizer() - 1);
            $newPlanet->setUser($colonize->getUser());
            $newPlanet->setName('Colonie');
            $newPlanet->setSoldier(50);
            $newPlanet->setScientist(0);
            $newPlanet->setNbColo(count($fleet->getUser()->getPlanets()) + 1);
            if($colonize->getNbrShips() == 0) {
                $em->remove($colonize);
            }
            $reportColo = new Report();
            $reportColo->setSendAt($now);
            $reportColo->setUser($user);
            $reportColo->setTitle("Colonisation de planète");
            $reportColo->setImageName("colonize_report.jpg");
            $reportColo->setContent("Vous venez de coloniser une planète inhabitée en : " .  $newPlanet->getSector()->getgalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . ". Cette planète fait désormais partit de votre Empire, pensez a la renommer sur la page Planètes.");
            $user->setViewReport(false);
            $em->persist($reportColo);
            $server->setNbrColonize($server->getNbrColonize() + 1);
            $quest = $user->checkQuests('colonize');
            if($quest) {
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                $user->removeQuest($quest);
            }
            $em->flush();
        }

        return $this->redirectToRoute('building', ['usePlanet' => $newPlanet->getId()]);
    }
}