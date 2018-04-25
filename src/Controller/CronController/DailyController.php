<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DailyController extends Controller
{
    /**
     * @Route("/medisdfssSDFdfda37jnhb=&dgsg4dsfgsd42556gd5gdsf&knbuih6=89&738=&273&74dsffDF9dfdfg90&7=64&7Y/", name="daily_load")
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
            $soldier = 0;
            $scientist = 0;
            foreach ($user->getPlanets() as $planet) {
                $worker = $worker + $planet->getWorker();
                $soldier = $soldier + $planet->getSoldier();
                $scientist = $scientist + $planet->getScientist();
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
            $ship = 0;
            $cost = $user->getBitcoin();
            $cost = $cost - ($soldier * 2) - ($ship / 10);
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
