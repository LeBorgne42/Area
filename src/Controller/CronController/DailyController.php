<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;
use DateTimeZone;
use App\Entity\Report;

class DailyController extends Controller
{
    /**
     * @Route("/dailyPlop/", name="daily_load")
     */
    public function dailyLoadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.rank', 'r')
            ->where('u.id != :one')
            ->setParameters(array('one' => 1))
            ->orderBy('r.point', 'DESC')
            ->getQuery()
            ->getResult();

        $x = 1;
        foreach ($users as $user) {
            $report = new Report();
            $report->setTitle("Rapport de l'empire");
            $report->setSendAt($now);
            $report->setUser($user);
            $ally = $user->getAlly();
            $worker = 0;
            $planetPoint= 0;
            $buildingCost = 0;
            foreach ($user->getPlanets() as $planet) {
                if($planet->getRadarAt() == null && $planet->getBrouilleurAt() == null) {
                    if (($planet->getWorker() + $planet->getWorkerProduction() > $planet->getWorkerMax())) {
                        $planet->setWorker($planet->getWorkerMax());
                    } else {
                        $planet->setWorker($planet->getWorker() + $planet->getWorkerProduction());
                    }
                    $worker = $worker + $planet->getWorker();
                    $planetPoint = $planetPoint + $planet->getBuildingPoint();
                    $buildingCost = $buildingCost + $planet->getBuildingCost();
                }
            }
            $gain = round($worker / 2);
            if($ally) {
                if($ally->getPeaces()) {
                    foreach($ally->getPeaces() as $peace) {
                        if($peace->getType() == false && $peace->getAccepted() == 1) {
                            $otherAlly = $em->getRepository('App:Ally')
                                ->createQueryBuilder('a')
                                ->where('a.sigle = :sigle')
                                ->setParameter('sigle', $peace->getAllyTag())
                                ->getQuery()
                                ->getOneOrNullResult();

                            $lose = (($peace->getTaxe() / 100) * $gain);
                            if($lose < 0) {
                                $lose = (($peace->getTaxe() / 100) * $user->getBitcoin());
                            }
                            $user->setBitcoin($user->getBitcoin() - $lose);
                            $otherAlly->setBitcoin($otherAlly->getBitcoin() + $lose);
                            $em->persist($otherAlly);
                            $em->persist($user);
                            $em->flush();
                        }
                    }
                }
                $userBitcoin = $user->getBitcoin();
                $taxe = (($ally->getTaxe() / 100) * $gain);
                $user->setBitcoin($userBitcoin - $taxe);
                $report->setContent("Le montant envoyé dans les fonds de votre alliance s'élève à " . round($taxe) . " Bitcoin.");
                $allyBitcoin = $ally->getBitcoin();
                $allyBitcoin = $allyBitcoin + $taxe;
                $ally->setBitcoin($allyBitcoin);
                $em->persist($ally);
            }
            $soldier = $user->getAllSoldier();
            $ship = $user->getAllShipsCost();
            $cost = $user->getBitcoin();
            $report->setContent($report->getContent() . "Le travaille fournit par vos travailleurs vous rapporte " . round($gain) . " Bitcoin.");
            $empireCost = ($soldier * 2) + $ship + $buildingCost;
            $cost = $cost - $empireCost + ($gain);
            $report->setContent($report->getContent() . " L'entretien de votre empire vous coûte cependant " . round($empireCost) . " Bitcoin.");
            $point = round(round($worker / 100) + round($user->getAllShipsPoint() / 75) + round($soldier / 75) + $planetPoint);
            $user->setBitcoin($cost);
            $report->setContent($report->getContent() . " Ce qui vous donne un revenu de " . round($gain - $empireCost) . " Bitcoin. Bonne journée Commandant.");
            $user->getRank()->setOldPoint($user->getRank()->getPoint());
            $user->getRank()->setPoint($point);
            $user->setViewReport(false);

            $em->persist($report);
            $em->persist($user);
            $x++;
        }
        $em->flush();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.rank', 'r')
            ->where('u.id != :one')
            ->setParameters(array('one' => 1))
            ->orderBy('r.point', 'DESC')
            ->getQuery()
            ->getResult();

        $x = 1;
        foreach ($users as $user) {
            $user->getRank()->setOldPosition($user->getRank()->getPosition());
            $user->getRank()->setPosition($x);
            $em->persist($user);
            $x++;
        }

        $allys = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->getQuery()
            ->getResult();
        foreach ($allys as $ally) {
            $ally->setRank($ally->getUsersPoint());
            $em->persist($ally);
        }

        $em->flush();

        exit;
    }
}
