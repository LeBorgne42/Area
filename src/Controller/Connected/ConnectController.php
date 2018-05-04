<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Rank;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class ConnectController extends Controller
{
    /**
     * @Route("/connection/", name="connect_server")
     */
    public function connectServerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $planet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user is null')
            ->andWhere('p.ground = :ground')
            ->andWhere('p.sky = :sky')
            ->andWhere('p.empty = :false')
            ->andWhere('p.cdr = :false')
            ->setParameters(array('ground' => 60, 'sky' => 10, 'false' => false))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        if($planet) {
            $planet->setUser($user);
            $planet->setName('Nova Terra');
            $planet->setSonde(10);
            $planet->setHunter(20);
            $planet->setNiobium(25000);
            $planet->setWater(30000);
            $planet->setFregate(5);
            $planet->setWorker(30000);
            $planet->setColonizer(1);
            $user->addPlanet($planet);
            $em->persist($planet);
        } else {
            return $this->redirectToRoute('logout');
        }

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.id = :id')
            ->setParameters(array('id' => 1))
            ->getQuery()
            ->getOneOrNullResult();

        $em->persist($planet);
        $rank = new Rank();
        $em->persist($rank);
        $user->setRank($rank);
        $salon->addUser($user);
        $em->persist($salon);
        $em->persist($user);
        $em->flush();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        return $this->redirectToRoute('login');
    }
}