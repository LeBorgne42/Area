<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RessourcesController extends Controller
{
    /**
     * @Route("/medisdfssSDFdfda37jnhb=&dgsgJHJH=UJHBJ=8==75IHBJHKNj=&knbuih6=89&738=&273&74dsffDF990&7=64&7Y/", name="ressources_load")
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
                $niobium = $planet->getNiobium();
                $water = $planet->getWater();
                $niobium = $niobium + ($planet->getBuilding()->getMiner()->getProduction());
                $water = $water + ($planet->getBuilding()->getExtractor()->getProduction());
                $planet->setNiobium($niobium);
                $planet->setWater($water);
                $em->persist($planet);
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