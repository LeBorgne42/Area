<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DailyController extends Controller
{
    /**
     * @Route("/dailyPlop/", name="daily_load")
     */
    public function dailyLoadAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.rank', 'r')
            ->orderBy('r.point', 'ASC')
            ->getQuery()
            ->getResult();

        $x = 1;
        foreach ($users as $user) {
            $ally = $user->getAlly();
            $worker = 0;
            foreach ($user->getPlanets() as $planet) {
                $planet->setWorker($planet->getWorker() * $planet->getWorkerProduction());
                $worker = $worker + $planet->getWorker();
            }
            if($ally) {
                $userBitcoin = $user->getBitcoin();
                $taxe = (($ally->getTaxe() / 200) * $worker);
                $user->setBitcoin($userBitcoin - $taxe);
                $allyBitcoin = $ally->getBitcoin();
                $allyBitcoin = $allyBitcoin + $taxe;
                $ally->setBitcoin($allyBitcoin);
                $em->persist($ally);
            }
            $soldier = $user->getAllSoldier();
            $ship = $user->getAllShips();
            $cost = $user->getBitcoin();
            $cost = $cost - ($soldier * 2) - ($ship / 10) + ($worker);
            $point = ($worker / 100) + ($ship / 5) + ($soldier);
            $user->setBitcoin($cost);
            $user->getRank()->setOldPoint($user->getRank()->getPoint());
            $user->getRank()->setPoint($point);
            $user->getRank()->setOldPosition($user->getRank()->getPosition());
            $user->getRank()->setPosition($x);

            $em->persist($user);
            $x++;
        }
        $em->flush();

        exit;
    }
}
