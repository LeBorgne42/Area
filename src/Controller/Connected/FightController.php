<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Report;
use App\Entity\Fleet;
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
                    if (in_array($fleetsWar->getUser()->getAlly()->getSigle(), $teamBlock) == false && $fleetsWar->getUser()->getAlly()->getSigleAlliedArray($teamBlock) &&
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
        $armorD = 0;
        $shieldD = 0;
        $missileD = 0;
        $laserD = 0;
        $plasmaD = 0;

        foreach($blockAtt as $attacker) {
            $armor = $armor + $attacker->getArmor();
            $shield = $shield + $attacker->getShield();
            $missile = $missile + $attacker->getMissile();
            $laser = $laser + $attacker->getLaser();
            $plasma = $plasma + $attacker->getPlasma();
            $debrisAtt = $attacker->getNbrSignatures();
            $armeSaveA = $missile + $laser + $plasma;
        }
        foreach($blockDef as $defender) {
            $armorD = $armorD + $defender->getArmor();
            $shieldD = $shieldD + $defender->getShield();
            $missileD = $missileD + $defender->getMissile();
            $laserD = $laserD + $defender->getLaser();
            $plasmaD = $plasmaD + $defender->getPlasma();
            $debrisDef = $defender->getNbrSignatures();
            $armeSaveB = $laserD + $plasmaD + $missileD;
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
                $reportLoseUtilA->setSendAt($now);
                $reportLoseUtilA->setContent("Votre flotte utilitaire " . $removeOne->getName() . " ne dispose pas des technologies nécessaires à l'identification des vaisseaux ennemis en " . $removeOne->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $removeOne->getPlanet()->getSector()->getPosition() . ":" . $removeOne->getPlanet()->getPosition() . " .");
                $reportLoseUtilA->setTitle("Rapport de combat : Défaite");
                $reportLoseUtilA->setUser($removeOne->getUser());
                $removeOne->getUser()->setViewReport(false);
                $em->persist($reportLoseUtilA);
                $em->remove($removeOne);
            }
            foreach($blockAtt as $reportWin) {
                $reportWinUtilA = new Report();
                $reportWinUtilA->setSendAt($now);
                $reportWinUtilA->setContent("Vous venez de détruire une flotte utilitaire en " . $reportWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $reportWin->getPlanet()->getSector()->getPosition() . ":" . $reportWin->getPlanet()->getPosition() . " .");
                $reportWinUtilA->setTitle("Rapport de combat : Victoire");
                $reportWin->getUser()->setViewReport(false);
                $reportWinUtilA->setUser($reportWin->getUser());
                $em->persist($reportWinUtilA);
            }
            $em->flush();
            return($blockAtt);
        }
        if($defAll > 0 && $attAll <= 0) {
            foreach($blockAtt as $removeTwo) {
                $reportLoseUtilB = new Report();
                $reportLoseUtilB->setSendAt($now);
                $reportLoseUtilB->setContent("Votre flotte utilitaire " . $removeTwo->getName() . " ne dispose pas des technologies nécessaires à l'identification des vaisseaux ennemis " . $removeTwo->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $removeTwo->getPlanet()->getSector()->getPosition() . ":" . $removeTwo->getPlanet()->getPosition() . " .");
                $reportLoseUtilB->setTitle("Rapport de combat : Défaite");
                $reportLoseUtilB->setUser($removeTwo->getUser());
                $removeTwo->getUser()->setViewReport(false);
                $em->persist($reportLoseUtilB);
                $em->remove($removeTwo);
            }
            foreach($blockDef as $reportWin) {
                $reportWinUtilB = new Report();
                $reportWinUtilB->setSendAt($now);
                $reportWinUtilB->setContent("Vous venez de détruire une flotte utilitaire en " . $reportWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $reportWin->getPlanet()->getSector()->getPosition() . ":" . $reportWin->getPlanet()->getPosition() . " .");
                $reportWinUtilB->setTitle("Rapport de combat : Victoire");
                $reportWinUtilB->setUser($reportWin->getUser());
                $reportWin->getUser()->setViewReport(false);
                $em->persist($reportWinUtilB);
            }
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
                $reportWinA->setContent($reportWinA->getContent() . "Vous avez gagné le combat en "  . $defenderWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderWin->getPlanet()->getSector()->getPosition() . ":" . $defenderWin->getPlanet()->getPosition() . " , vous remportez " . $warPointA . " points de Guerre");
                $defenderWin->getUser()->setViewReport(false);
                $em->persist($reportWinA);
            }
            foreach($blockAtt as $attackerLose) {
                $reportLoseA = new Report();
                $reportLoseA->setSendAt($now);
                $reportLoseA->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                $reportLoseA->setTitle("Rapport de combat : Défaite");
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
                $reportLoseA->setContent($reportLoseA->getContent() . "Vous avez perdu le combat en " . $attackerLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerLose->getPlanet()->getSector()->getPosition() . ":" . $attackerLose->getPlanet()->getPosition() . " , vos adversaires remportent " . $warPointA . " points de Guerre.");
                $attackerLose->getUser()->setViewReport(false);
                $planet = $attackerLose->getPlanet();

                $loseArm = $attackerLose->getLaser() + $attackerLose->getMissile() + $attackerLose->getPlasma();
                $percentWarPoint = ($loseArm * 100) / $armeSaveA;
                $warPointB = ($warPointB - ($armorD / 80)) / 10;
                $newWarPoint = round(($percentWarPoint * $warPointB) / 100);
                if($newWarPoint < 0) {
                    $newWarPoint = $newWarPoint * -1;
                }
                if($attackerLose->getUser()->getPeaces()) {
                    $peace = $attackerLose->getUser()->getPeaces();
                    $pdgPeace = $newWarPoint * ($peace->getPdg() / 100);
                    $newWarPoint = $newWarPoint - $pdgPeace;
                    $otherAlly = $em->getRepository('App:Ally')
                        ->createQueryBuilder('a')
                        ->where('a.sigle = :sigle')
                        ->setParameter('sigle', $peace->getAllyTag())
                        ->getQuery()
                        ->getOneOrNullResult();
                    $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                }
                $reportLoseA->setContent($reportLoseA->getContent() . " Mais vous remportez vous même " . $newWarPoint . " points de Guerre !");
                if($attackerLose->getUser()->getRank()) {
                    $attackerLose->getUser()->getRank()->setWarPoint($attackerLose->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $em->persist($reportLoseA);
                $em->remove($attackerLose);
            }
            foreach($blockDef as $defenderWin) {
                $defArm = $defenderWin->getLaser() + $defenderWin->getMissile() + $defenderWin->getPlasma();
                $percentWarPoint = ($defArm * 100) / $armeSaveB;
                $newWarPoint = round(($percentWarPoint * $warPointA) / 100);
                if($defenderWin->getUser()->getPeaces()) {
                    $peace = $defenderWin->getUser()->getPeaces();
                    $pdgPeace = $newWarPoint * ($peace->getPdg() / 100);
                    $newWarPoint = $newWarPoint - $pdgPeace;
                    $otherAlly = $em->getRepository('App:Ally')
                        ->createQueryBuilder('a')
                        ->where('a.sigle = :sigle')
                        ->setParameter('sigle', $peace->getAllyTag())
                        ->getQuery()
                        ->getOneOrNullResult();
                    $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
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
                $reportWinB->setSendAt($now);
                $reportWinB->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSDef . " tir(s) pour percer les boucliers</th></tr>");
                $reportWinB->setTitle("Rapport de combat : Victoire");
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
                $reportWinB->setContent($reportWinB->getContent() . "Vous avez gagné le combat en "  . $attackerWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerWin->getPlanet()->getSector()->getPosition() . ":" . $attackerWin->getPlanet()->getPosition() . " , vous remportez " . $warPointB . " points de Guerre");
                $attackerWin->getUser()->setViewReport(false);
                $em->persist($reportWinB);
            }
            foreach($blockDef as $defenderLose) {
                $reportLoseB = new Report();
                $reportLoseB->setSendAt($now);
                $reportLoseB->setContent("<table class=\"table table-striped table-bordered table-dark\"><tbody><tr><th class=\"tab-cells-name p-1 ml-2\">Groupe de combat 1</th><th class=\"tab-cells-name p-1 ml-2\">" . $countSAtt . " tir(s) pour percer les boucliers</th></tr>");
                $reportLoseB->setTitle("Rapport de combat : Défaite");
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
                $reportLoseB->setContent($reportLoseB->getContent() . "Vous avez perdu le combat en " . $defenderLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderLose->getPlanet()->getSector()->getPosition() . ":" . $defenderLose->getPlanet()->getPosition() . " , vos adversaires remportent " . $warPointB . " points de Guerre.");
                $defenderLose->getUser()->setViewReport(false);
                $planet = $defenderLose->getPlanet();
                $loseArm = $defenderLose->getLaser() + $defenderLose->getMissile() + $defenderLose->getPlasma();
                $percentWarPoint = ($loseArm * 100) / $armeSaveB;
                $warPointA = ($warPointA - ($armor / 80)) / 10;
                $newWarPoint = round(($percentWarPoint * $warPointA) / 100);
                if($newWarPoint < 0) {
                    $newWarPoint = $newWarPoint * -1;
                }
                if($defenderLose->getUser()->getPeaces()) {
                    $peace = $defenderLose->getUser()->getPeaces();
                    $pdgPeace = $newWarPoint * ($peace->getPdg() / 100);
                    $newWarPoint = $newWarPoint - $pdgPeace;
                    $otherAlly = $em->getRepository('App:Ally')
                        ->createQueryBuilder('a')
                        ->where('a.sigle = :sigle')
                        ->setParameter('sigle', $peace->getAllyTag())
                        ->getQuery()
                        ->getOneOrNullResult();
                    $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
                }
                $reportLoseB->setContent($reportWinB->getContent() . " Mais vous remportez vous même " . $newWarPoint . " points de Guerre !");
                if($defenderLose->getUser()->getRank()) {
                    $defenderLose->getUser()->getRank()->setWarPoint($defenderLose->getUser()->getRank()->getWarPoint() + $newWarPoint);
                }
                $em->persist($reportLoseB);
                $em->remove($defenderLose);
            }
            foreach($blockAtt as $attackerWin) {
                $attArm = $attackerWin->getLaser() + $attackerWin->getMissile() + $attackerWin->getPlasma();
                $percentWarPoint = ($attArm * 100) / $armeSaveA;
                $newWarPoint = round(($percentWarPoint * $warPointB) / 100);
                if($attackerWin->getUser()->getPeaces()) {
                    $peace = $attackerWin->getUser()->getPeaces();
                    $pdgPeace = $newWarPoint * ($peace->getPdg() / 100);
                    $newWarPoint = $newWarPoint - $pdgPeace;
                    $otherAlly = $em->getRepository('App:Ally')
                        ->createQueryBuilder('a')
                        ->where('a.sigle = :sigle')
                        ->setParameter('sigle', $peace->getAllyTag())
                        ->getQuery()
                        ->getOneOrNullResult();
                    $otherAlly->setPdg($otherAlly->getPdg() + $pdgPeace);
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
       * @Route("/hello-we-come-for-you/{idp}/{fleet}/", name="invader_planet", requirements={"idp"="\d+", "fleet"="\d+"})
       */
    public function invaderAction($idp, $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $invader = $em->getRepository('App:Fleet')->find(['id' => $fleet]);
        $barge = $invader->getBarge() * 2500;
        $defenser = $invader->getPlanet();
        $userDefender= $invader->getPlanet()->getUser();
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

        if($barge && $invader->getPlanet()->getUser() && $invader->getAllianceUser() && $invader->getFightAt() == null && $invader->getFlightTime() == null && $user->getSigleAlliedArray($dSigle)) {
            if($barge >= $invader->getSoldier()) {
                $aMilitary = $invader->getSoldier() * $alea;
                $soldierAtmp = $invader->getSoldier();
            } else {
                $aMilitary = $barge * $alea;
                $soldierAtmp = $barge;
            }
            if($dMilitary > $aMilitary) {
                $aMilitary = ($defenser->getSoldier() * 6) - $aMilitary;
                if($barge < $invader->getSoldier()) {
                    $invader->setSoldier($invader->getSoldier() - $barge);
                }
                $defenser->setBarge($defenser->getBarge() + $invader->getBarge());
                $invader->setBarge(0);
                if($aMilitary < 0) {
                    $soldierDtmp = $defenser->getSoldier();
                    $workerDtmp = $defenser->getWorker();
                    $defenser->setSoldier(0);
                    $defenser->setWorker($defenser->getWorker() + $aMilitary);
                    $soldierDtmp = $soldierDtmp - $defenser->getSoldier();
                    $workerDtmp = $workerDtmp - $defenser->getWorker();
                } else {
                    $defenser->setSoldier(round($aMilitary / 6));
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
                $soldierAtmp = $invader->getSoldier();
                $invader->setSoldier(($aMilitary - $dMilitary) / $alea);
                $soldierAtmp = $soldierAtmp - $invader->getSoldier();
                $defenser->setSoldier(0);
                $defenser->setWorker(2000);
                if($invader->getUser()->getColPlanets() <= ($invader->getUser()->getTerraformation() + 1)) {
                    $defenser->setUser($user);
                    $em->flush();
                } else {
                    $hydra = $em->getRepository('App:User')->find(['id' => 1]);

                    $defenser->setUser($hydra);
                    $defenser->setWorker(25000);
                    $defenser->setSoldier(500);
                    $defenser->setName('Avant Poste');
                    $em->flush();
                }
                if($userDefender->getColPlanets() == 0) {
                    $userDefender->setGameOver($user->getUserName());
                    $userDefender->setAlly(null);
                    $userDefender->setGrade(null);
                    foreach($userDefender->getFleets() as $tmpFleet) {
                        $tmpFleet->setUser($user);
                    }
                }
                $reportDef->setTitle("Rapport d'invasion : Défaite (défense)");
                $reportDef->setContent("Mais QUI ? QUI !!! Vous as donné un commandant si médiocre " . $user->getUserName() . " n'a pas eu a faire grand chose pour prendre votre planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . ".  " . round($soldierAtmp) . " soldats ennemis sont tout de même éliminé. C'est toujours ça de gagner. Vos " . $soldierDtmp . " soldats et " . $workerDtmp . " travailleurs sont tous mort. Votre empire en a prit un coup, mais il vous reste des planètes, il est l'heure de la revanche !");
                $reportInv->setTitle("Rapport d'invasion : Victoire (attaque)");
                $reportInv->setContent("Vous débarquez après que la planète ait été prise et vous installez sur le trône de " . $userDefender->getUserName() . ". Qu'il est bon d'entendre ses pleures lointain... La planète " . $defenser->getName() . " - " . $defenser->getSector()->getgalaxy()->getPosition() . ":" . $defenser->getSector()->getPosition() . ":" . $defenser->getPosition() . " est désormais votre! Il est temps de remettre de l'ordre dans la galaxie. " . round($soldierAtmp) . " de vos soldats ont péri dans l'invasion. Mais les défenseurs ont aussi leurs pertes : " . $soldierDtmp . " soldats et " . $workerDtmp . " travailleurs ont péri. Cependant vous épargnez 2000 travailleurs dans votre bonté (surtout pour faire tourner la planète).");
            }
            if($invader->getNbrShips() == 0) {
                $em->remove($invader);
            }
            $server->setNbrInvasion($server->getNbrInvasion() + 1);
            $em->persist($reportInv);
            $em->persist($reportDef);
            $em->flush();
        }

        return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $defenser->getSector()->getPosition(), 'gal' => $defenser->getSector()->getGalaxy()->getPosition()]);
    }

    /**
     * @Route("/colonisation-planete/{idp}/{fleet}/", name="colonizer_planet", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function colonizeAction($idp, Fleet $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $colonize = $em->getRepository('App:Fleet')->find(['id' => $fleet]);
        $newPlanet = $colonize->getPlanet();

        if($colonize->getColonizer() && $newPlanet->getUser() == null &&
            $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
            $newPlanet->getCdr() == false && $colonize->getUser()->getColPlanets() < 21 &&
            $colonize->getUser()->getColPlanets() <= ($user->getTerraformation() + 1)) {
            $colonize->setColonizer($colonize->getColonizer() - 1);
            $newPlanet->setUser($colonize->getUser());
            $newPlanet->setName('Colonie');
            $newPlanet->setSoldier(0);
            $newPlanet->setScientist(0);
            $newPlanet->setNbColo(count($fleet->getUser()->getPlanets()) + 1);
            if($colonize->getNbrShips() == 0) {
                $em->remove($colonize);
            }
            $reportColo = new Report();
            $reportColo->setSendAt($now);
            $reportColo->setUser($user);
            $reportColo->setTitle("Colonisation de planète");
            $reportColo->setContent("Vous venez de coloniser une planète inhabitée en : " .  $newPlanet->getSector()->getgalaxy()->getPosition() . ":" . $newPlanet->getSector()->getPosition() . ":" . $newPlanet->getPosition() . ". Cette planète fait désormais partit de votre Empire, pensez a la renommer sur la page Planètes.");
            $user->setViewReport(false);
            $em->persist($reportColo);
            $server->setNbrColonize($server->getNbrColonize() + 1);
            $em->flush();
        }

        return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $newPlanet->getSector()->getPosition(), 'gal' => $newPlanet->getSector()->getGalaxy()->getPosition()]);
    }
}