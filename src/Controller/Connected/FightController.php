<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Report;
use DateTime;
use DateTimeZone;

class FightController extends Controller
{
    /**
     * @Route("/clash/", name="fight_war_area")
     */
    public function fightAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $winner = null;

        $firstFleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->select('p.id')
            ->where('f.fightAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        if(!$firstFleet) {
            exit;
        }

        $fleetsWars = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('p.id = :id')
            ->setParameters(array('id' => $firstFleet['id']))
            ->orderBy('f.attack', 'ASC')
            ->getQuery()
            ->getResult();

        $teamBlock = [];
        foreach ($fleetsWars as $fleetsWar) {
            if($fleetsWar->getUser()->getAlly()) {
                if (in_array($fleetsWar->getUser()->getAlly()->getSigle(), $teamBlock) == false && $fleetsWar->getUser()->getAlly()->getSigleAlliedArray($teamBlock)) {
                    $teamBlock[] = $fleetsWar->getUser()->getAlly()->getSigle();
                }
            } elseif (in_array($fleetsWar->getUser()->getUserName(), $teamBlock) == false) {
                $teamBlock[] = $fleetsWar->getUser()->getUserName();
            }
        }
        $tmpcount = count($teamBlock);
        if($tmpcount < 2) {
            foreach ($fleetsWars as $fleetsWar) {
                $fleetsWar->setFightAt(null);
                $em->persist($fleetsWar);
                $em->flush();
            }
            exit;
        }
        $team = $tmpcount;
        $isAttack = [];

        while($team > 0) {
            $team--;
            ${'oneBlock'.$team} = new \ArrayObject();
            foreach ($fleetsWars as $fleetsWar) {
                if($fleetsWar->getUser()->getAlly()) {
                    if ($teamBlock[$team] == $fleetsWar->getUser()->getAlly()->getSigle() || $fleetsWar->getUser()->getAlly()->getSigleAllied($teamBlock[$team])) {
                        ${'oneBlock'.$team}->append($fleetsWar);
                        $isAttack[$team] = $fleetsWar->getAttack();
                    }
                } elseif ($teamBlock[$team] == $fleetsWar->getUser()->getUserName()) {
                    ${'oneBlock'.$team}->append($fleetsWar);
                    $isAttack[$team] = $fleetsWar->getAttack();
                }
            }
        }
        $team1 = $tmpcount - 1;
        $team2 = $tmpcount - 2;

        if ($isAttack[$team1] == true || $isAttack[$team2] == true) {
            $winner = self::attackAction(${'oneBlock'.$team1}, ${'oneBlock'.$team2});
        } else {
            while($isAttack[$team2--] == true) {
                $winner = self::attackAction(${'oneBlock'.$team1}, ${'oneBlock'.$team2});
            }
        }
        if($winner != null) {
            if($winner == ${'oneBlock'.$team1}) {
                $team2 = $team2 - 1;
                ${'oneBlock'.$team1} = $winner;
            } elseif ($winner == ${'oneBlock'.$team2}) {
                $team1 = $team1 - $team2;
                ${'oneBlock'.$team2} = $winner;
            }
        } else {
            foreach ($fleetsWars as $fleetsWar) {
                $fleetsWar->setFightAt(null);
                $em->persist($fleetsWar);
                $em->flush();
            }
            exit;
        }

        $team = $tmpcount - 2;
        while($team > 0) {
            $winner = self::attackAction(${'oneBlock'.$team1}, ${'oneBlock'.$team2});
            $team--;
            if($winner == ${'oneBlock'.$team1}) {
                $team2 = $team2 - 1;
                ${'oneBlock'.$team1} = $winner;
            } elseif ($winner == ${'oneBlock'.$team2}) {
                $team1 = $team1 - $team2;
                ${'oneBlock'.$team2} = $winner;
            }
        }
        $em->flush();
        exit;
      }


    public function attackAction($blockAtt, $blockDef)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

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
        }
        foreach($blockDef as $defender) {
            $armorD = $armorD + $defender->getArmor();
            $shieldD = $shieldD + $defender->getShield();
            $missileD = $missileD + $defender->getMissile();
            $laserD = $laserD + $defender->getLaser();
            $plasmaD = $plasmaD + $defender->getPlasma();
            $debrisDef = $defender->getNbrSignatures();
        }
        $firstBlood = (($plasma * 2) + $laser);
        $firstBloodD = (($plasmaD * 2) + $laserD);
        $countSAtt = 0;
        $countSDef = 0;

        if(($plasma + $laser > 0) && $shieldD > 0) {
            while ($shieldD > 0) {
                $countSAtt++;
                $shieldD = $shieldD - $firstBlood;
            }
            $armorD = $armorD - $firstBlood;
        } elseif ($shieldD < 0) {
            $countSAtt = 1;
            $armorD = $armorD - $firstBlood;
        }
        if(($plasmaD + $laserD > 0) && $shield > 0) {
            while($shield > 0) {
                $countSDef++;
                $shield = $shield - $firstBloodD;
            }
            $armor = $armor - $firstBloodD;
        } elseif ($shieldD < 0) {
            $countSAtt = 1;
            $armor = $armor - $firstBloodD;
        }
        $secondShot = ($missile + $plasma + $laser);
        $secondShotD = ($missileD + $plasmaD + $laserD);
        if($countSAtt - $countSDef > 0) {
            $armorD = $armorD - ($firstBlood * ($countSAtt - $countSDef));
            $secondShot = ($missile + $plasma + $laser) - ($firstBlood * ($countSAtt - $countSDef));
        }
        if($countSDef - $countSAtt > 0) {
            $armor = $armor - ($firstBloodD * ($countSDef - $countSAtt));
            $secondShotD = ($missileD + $plasmaD + $laserD) - ($firstBloodD * ($countSDef - $countSAtt));
        }
        while((($missileD + $plasmaD + $laserD > 0) && $armor > 0) &&
            (($missile + $plasma + $laser > 0) && $armorD > 0) && ($shieldD <= 0 && $shield <= 0)) {
            if ($armorD > 0) {
                $armorD = $armorD - $secondShot;
                $tmpSecondShotD = $secondShotD;
                $secondShot = ($missile + $plasma + $laser) - ($tmpSecondShotD / 2);
            }
            if($armor > 0) {
                $armor = $armor - $secondShotD;
                $secondShotD = ($missileD + $plasmaD + $laserD) - ($secondShot / 2);
            }
        }
        if($shieldD > 0) {
            $armor = 0;
        }
        if($shield > 0) {
            $armorD = 0;
        }
        $attReport = '';
        $defReport = '';
        foreach($blockAtt as $tmpreport) {
            $attReport = $attReport . $tmpreport->getUser()->getUserName() . ', ';
        }
        foreach($blockDef as $tmpreport) {
            $defReport = $defReport . $tmpreport->getUser()->getUserName() . ', ';
        }
        if ($armorD > $armor || $shieldD > 0) {
            foreach($blockAtt as $attackerLose) {
                $report = new Report();
                $report->setTitle("Rapport de combat : Défaite");
                $report->setSendAt($now);
                $report->setUser($attackerLose->getUser());
                $report->setContent($report->getContent() . "Tandis que vous " . $attReport . " exploriez tranquillement la galaxie en " . $attackerLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerLose->getPlanet()->getSector()->getPosition() . ":" . $attackerLose->getPlanet()->getPosition() . " les flottes de " . $defReport . " vous ont littéralement VIOLÉES ! Mais c'est pas grave on est à la beta et ceci un rapport temporaire.");
                $attackerLose->getUser()->setViewReport(false);
                $planet = $attackerLose->getPlanet();
                $em->persist($report);
                $em->remove($attackerLose);
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisAtt * 1.15));
            $planet->setWtCdr($planet->getWtCdr() + $debrisAtt);
            $em->persist($planet);
            foreach($blockDef as $defenderWin) {
                $report = new Report();
                $report->setTitle("Rapport de combat : Victoire");
                $report->setSendAt($now);
                $report->setUser($defenderWin->getUser());
                $report->setContent($report->getContent() . "C'est une victoire pour vous " . $defReport . "! Les dirigeants " . $attReport . " n'ont pas fait le poids face a votre flotte en "  . $defenderWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderWin->getPlanet()->getSector()->getPosition() . ":" . $defenderWin->getPlanet()->getPosition() .  " vous vous imposez toujours un peu plus dans cette galaxie lointaine jusqu'à atteindre les sommets !");
                $defenderWin->getUser()->setViewReport(false);
                $em->persist($report);
                if ($defenderWin->getArmor() != $armorD) {
                    $malus = ($defenderWin->getArmor()) / (($defenderWin->getArmor() - $armorD) / rand(1, 3));
                    $defenderWin->setFleetWinRatio(number_format($malus, 2));
                    $defenderWin->setFightAt(null);
                    $em->persist($defenderWin);
                }
            }
            return($blockDef);
        } else {
            foreach($blockDef as $defenderLose) {
                $report = new Report();
                $report->setTitle("Rapport de combat : Défaite");
                $report->setSendAt($now);
                $report->setUser($defenderLose->getUser());
                $report->setContent($report->getContent() . "Tandis que vous " . $defReport . " exploriez tranquillement la galaxie en " . $defenderLose->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $defenderLose->getPlanet()->getSector()->getPosition() . ":" . $defenderLose->getPlanet()->getPosition() .  ", les flottes de " . $attReport . " vous ont littéralement VIOLÉES ! Mais c'est pas grave on est à la beta et ceci un rapport temporaire.");
                $defenderLose->getUser()->setViewReport(false);
                $planet = $defenderLose->getPlanet();
                $em->persist($report);
                $em->remove($defenderLose);
            }
            $planet->setNbCdr($planet->getNbCdr() + ($debrisDef * 1.15));
            $planet->setWtCdr($planet->getWtCdr() + $debrisDef);
            $em->persist($planet);
            foreach($blockAtt as $attackerWin) {
                $report = new Report();
                $report->setTitle("Rapport de combat : Victoire");
                $report->setSendAt($now);
                $report->setUser($attackerWin->getUser());
                $report->setContent($report->getContent() . "C'est une victoire pour vous " . $attReport . "! Les dirigeants " . $defReport . " n'ont pas fait le poids face a votre flotte en "  . $attackerWin->getPlanet()->getSector()->getGalaxy()->getPosition() . ":" . $attackerWin->getPlanet()->getSector()->getPosition() . ":" . $attackerWin->getPlanet()->getPosition() .  " vous vous imposez toujours un peu plus dans cette galaxie lointaine jusqu'à atteindre les sommets !");
                $attackerWin->getUser()->setViewReport(false);
                $em->persist($report);
                if($attackerWin->getArmor() != $armor) {
                    $malus = ($attackerWin->getArmor()) / (($attackerWin->getArmor() - $armor) / rand(1, 2));
                    $attackerWin->setFleetWinRatio(number_format($malus, 2));
                    $attackerWin->setFightAt(null);
                    $em->persist($attackerWin);
                }
            }
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
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $invader = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $barge = $invader->getBarge() * 2500;
        $defenser = $invader->getPlanet();
        $userDefender= $invader->getPlanet()->getUser();
        $dMilitary = $defenser->getWorker() + ($defenser->getSoldier() * 6);
        $alea = rand(5, 9);


        $reportInv = new Report();
        $reportInv->setSendAt($now);
        $reportInv->setUser($user);
        $user->setViewReport(false);


        $reportDef = new Report();
        $reportDef->setSendAt($now);
        $reportDef->setUser($userDefender);
        $userDefender->setViewReport(false);

        if($barge and $invader->getPlanet()->getUser() and $invader->getAllianceUser() == null) {
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
                    $defenser->setSoldier($aMilitary / 6);
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
                if(count($invader->getUser()->getPlanets()) < 21) {
                    $defenser->setUser($user);
                } else {
                    $defenser->setUser(null);
                    $defenser->setName('Abandonnée');
                }
                if(count($userDefender->getPlanets()) == 1) {
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
            $em->persist($invader);
            if($invader->getNbrShips() == 0) {
                $em->remove($invader);
            }
            $em->persist($reportInv);
            $em->persist($reportDef);
            $em->persist($defenser);
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $defenser->getSector()->getPosition()));
    }

    /**
     * @Route("/colonisation-planete/{idp}/{fleet}/", name="colonizer_planet", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function colonizeAction($idp, $fleet)
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

        $colonize = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $newPlanet = $colonize->getPlanet();
        if($colonize->getColonizer() && $newPlanet->getUser() == null &&
            $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
            $newPlanet->getCdr() == false && count($colonize->getUser()->getPlanets()) < 21 &&
            count($colonize->getUser()->getPlanets()) <= ($user->getTerraformation() + 2)) {
            $colonize->setColonizer($colonize->getColonizer() - 1);
            $newPlanet->setUser($colonize->getUser());
            $newPlanet->setName('Colonie');
            $em->persist($colonize);
            if($colonize->getNbrShips() == 0) {
                $em->remove($colonize);
            }
            $em->persist($newPlanet);
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $newPlanet->getSector()->getPosition()));
    }
}