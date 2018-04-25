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
            foreach ($user->getPlanets() as $planet) {
                $worker = $worker + $planet->getWorker();
                $soldier = $soldier + $planet->getSoldier();
            }
            if($ally) {
                $userBitcoin = $user->getBitcoin();
                $userBitcoin = $userBitcoin - (($ally->getTaxe() / 200) * $worker);
                $user->setBitcoin($userBitcoin);
                $allyBitcoin = $ally->getBitcoin();
                $allyBitcoin = $allyBitcoin + $userBitcoin;
                $ally->setBitcoin($allyBitcoin);
                $em->persist($ally);
            }
            $cost = $user->getBitcoin();
            $cost = $cost - ($soldier * 2);
            $point = ($worker / 100);
            $user->setBitcoin($cost);
            $user->getRank()->setOldPoint($user->getRank()->getPoint());
            $user->getRank()->setPoint($point - $user->getRank()->getPoint());
            $user->getRank()->setOldPosition($user->getRank()->getPosition());
            $user->getRank()->setPosition($x);

            $em->persist($user);
            $x++;
        }
        $em->flush();

        return $this->redirectToRoute('home');
    }
}
