<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
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

        $firstFleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->select('p.id')
            ->where('f.fightAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->setMaxResults(1)
            ->getResult();

        if(!$firstFleet) {
            exit;
        }

        $fleetsWars = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('p.id = :id')
            ->setParameters(array('id' => $firstFleet))
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

        if($winner == ${'oneBlock'.$team1}) {
            $team2 = $team2 - 1;
        } elseif ($winner == ${'oneBlock'.$team2}) {
            $team1 = $team1 - $team2;
        }

        $team = $tmpcount - 2;
        while($team > 0) {
            $winner = self::attackAction(${'oneBlock'.$team1}, ${'oneBlock'.$team2});
            $team--;
            if($winner == ${'oneBlock'.$team1}) {
                $team2 = $team2 - 1;
            } elseif ($winner == ${'oneBlock'.$team2}) {
                $team1 = $team1 - $team2;
            }
        }
        $em->flush();
        exit;
      }


    public function attackAction($blockAtt, $blockDef)
    {
        $em = $this->getDoctrine()->getManager();

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
        }
        foreach($blockDef as $defender) {
            $armorD = $armorD + $defender->getArmor();
            $shieldD = $shieldD + $defender->getShield();
            $missileD = $missileD + $defender->getMissile();
            $laserD = $laserD + $defender->getLaser();
            $plasmaD = $plasmaD + $defender->getPlasma();
        }
        $firstBlood = ($plasma + $laser);
        $firstBloodD = ($plasmaD + $laserD);
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
            (($missile + $plasma + $laser > 0) && $armorD > 0)) {
            if ($armorD > 0) {
                $armorD = $armorD - $secondShot;
            }
            if($armor > 0) {
                $armor = $armor - $secondShotD;
            }
            $tmpSecondShotD = ($missileD + $plasmaD + $laserD);
            $secondShotD = ($missileD + $plasmaD + $laserD) - ($secondShot / 2);
            $secondShot = ($missile + $plasma + $laser) - ($tmpSecondShotD / 2);
        }
        if ($armorD > $armor) {
            foreach($blockAtt as $attackerLose) {
                $em->remove($attackerLose);
            }
            foreach($blockDef as $defenderWin) {
                if ($defenderWin->getArmor() != $armorD) {
                    $malus = ($defenderWin->getArmor()) / (($defenderWin->getArmor() - $armorD) / rand(2, 4));
                    $defenderWin->setFleetWinRatio(number_format($malus, 2));
                    $em->persist($defenderWin);
                }
            }
            return($blockDef);
        } else {
            foreach($blockDef as $defenderLose) {
                $em->remove($defenderLose);
            }
            foreach($blockAtt as $attackerWin) {
                if($attackerWin->getArmor() != $armor) {
                    $malus = ($attackerWin->getArmor()) / (($attackerWin->getArmor() - $armor) / rand(2, 3));
                    $attackerWin->setFleetWinRatio(number_format($malus, 2));
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
        if($barge and $invader->getPlanet()->getUser() and $invader->getAllianceUser() == null) {
            if($barge >= $invader->getSoldier()) {
                $aMilitary = $invader->getSoldier() * $alea;
            } else {
                $aMilitary = $barge * $alea;
            }
            if($dMilitary > $aMilitary) {
                $aMilitary = ($defenser->getSoldier() * 6) - $aMilitary;
                $invader->setSoldier(0);
                $defenser->setBarge($defenser->getBarge() + $invader->getBarge());
                $invader->setBarge(0);
                if($aMilitary < 0) {
                    $defenser->setSoldier(0);
                    $defenser->setWorker($defenser->getWorker() + $aMilitary);
                } else {
                    $defenser->setSoldier($aMilitary / 6);
                }
            } else {
                $invader->setSoldier(($aMilitary - $dMilitary) / $alea);
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
            }
            $em->persist($invader);
            if($invader->getNbrShips() == 0) {
                $em->remove($invader);
            }
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
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
            $newPlanet->getCdr() == false && count($colonize->getUser()->getPlanets()) < 21) {
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