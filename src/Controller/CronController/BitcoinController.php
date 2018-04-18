<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserContactType;

class BitcoinController extends Controller
{
    /**
     * @Route("/medisdfssSDFdfda37jnhb=&dgsgJHJH=UJHBJ=8==75IHBJHKNj=&knbuih6=89&738=&273&74dsffDF990&7=64&7Y/", name="bitcoin_load")
     */
    public function bitcoinLoadAction()
    {
        /*$em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.planets', 'p')
            ->join('p.worker', 'w')
            ->select('u.bitcoin, w.amount, p.user')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $bitcoin = $user['bitcoin'];
            $bitcoin = $bitcoin + ($user['amount'] / 17500);
            $user->setBitcoin($bitcoin);
            $em->persist($user);
        }
        $em->flush();

        return $this->render('anonymous/media.html.twig');*/

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $worker = 0;
            foreach ($user->getPlanets() as $planet) {
            $worker = $worker + $planet->getWorker()->getAmount();
            }
            $bitcoin = $user->getBitcoin();
            $bitcoin = $bitcoin + ($worker / 175000);
            $user->setBitcoin($bitcoin);
            $em->persist($user);
        }
        $em->flush();

        return $this->render('anonymous/media.html.twig');
    }
}
