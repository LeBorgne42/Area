<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MissionType;
use App\Form\Front\MissionUraType;
use App\Entity\Planet;
use App\Entity\Report;
use App\Entity\Mission;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ZombieController extends AbstractController
{
    /**
     * @Route("/zombie/{usePlanet}", name="zombie", requirements={"usePlanet"="\d+"})
     */
    public function soldierAction(Request $request, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $planet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy('p.ground', 'ASC')
            ->getQuery()
            ->setMaxresults(1)
            ->getOneOrNullResult();

        $form_missionUranium = $this->createForm(MissionUraType::class);
        $form_missionUranium->handleRequest($request);

        $form_missionZombie = $this->createForm(MissionType::class);
        $form_missionZombie->handleRequest($request);

        if ($form_missionZombie->isSubmitted() && $form_missionZombie->isValid()) {
            $soldier = abs($form_missionZombie->get('soldier')->getData());
            $tank = abs($form_missionZombie->get('tank')->getData());
            $time = abs($form_missionZombie->get('time')->getData());
            $zombie = $user->getZombieATT() * 75;
            $zombieTotal = $zombie;
            $zombie = $zombie - $soldier - $tank;
            $alea = rand(1, 100) == 100 ? 2 : 1;
            if ($zombie <= 1) {
                $zombie = 1;
            } else {
                $zombie = 1 + ((100 * $zombie) / $zombieTotal) / 100;
            }
            if ($time == 1) {
                $percent = ROUND(90 / $zombie);
                $gain = 1;
            } elseif ($time == 3) {
                $percent = ROUND(70 / $zombie);
                $gain = 5;
            } elseif ($time == 6) {
                $percent = ROUND(50 / $zombie);
                $gain = 8;
            } elseif ($time == 10) {
                $percent = ROUND(30 / $zombie);
                $gain = 15;
            }
            if ($soldier > $planet->getSoldier() || $tank > $planet->getTank() || !$gain || ($soldier == 0 && $tank == 0) || count($planet->getMissions()) >= 3) {
                return $this->redirectToRoute('zombie', ['usePlanet' => $usePlanet->getId()]);
            } else {
                $percent = rand(1, 100) > $percent ? 0 : 1;
                $now->add(new DateInterval('PT' . $time . 'H'));
                $mission = new Mission();
                $mission->setMissionAt($now);
                $mission->setType(0);
                $mission->setPlanet($planet);
                $mission->setSoldier($soldier);
                $mission->setTank($tank);
                $mission->setWin($percent);
                $mission->setGain($gain * $alea);
                $planet->setSoldier($planet->getSoldier() - $soldier);
                $planet->setTank($planet->getTank() - $tank);
                $em->persist($mission);
                $em->flush();
            }
            return $this->redirectToRoute('zombie', ['usePlanet' => $usePlanet->getId()]);
        }

        if ($form_missionUranium->isSubmitted() && $form_missionUranium->isValid()) {
            $soldier = abs($form_missionUranium->get('soldier')->getData());
            $tank = abs($form_missionUranium->get('tank')->getData());
            $time = abs($form_missionUranium->get('time')->getData());
            $zombie = $user->getZombieATT() * 75;
            $zombieTotal = $zombie;
            $zombie = $zombie - $soldier - $tank;
            $alea = rand(1, 100) == 100 ? 2 : 1;
            if ($zombie <= 1) {
                $zombie = 1;
            } else {
                $zombie = 1 + ((100 * $zombie) / $zombieTotal) / 100;
            }
            if ($time == 1) {
                $percent = ROUND(90 / $zombie);
                $gain = 1;
            } elseif ($time == 3) {
                $percent = ROUND(70 / $zombie);
                $gain = 5;
            } elseif ($time == 6) {
                $percent = ROUND(50 / $zombie);
                $gain = 8;
            } elseif ($time == 10) {
                $percent = ROUND(30 / $zombie);
                $gain = 15;
            }
            if ($soldier > $planet->getSoldier() || $tank > $planet->getTank() || !$gain || ($soldier == 0 && $tank == 0) || count($planet->getMissions()) >= 3) {
                return $this->redirectToRoute('zombie', ['usePlanet' => $usePlanet->getId()]);
            } else {
                $percent = rand(1, 100) > $percent ? 0 : 1;
                $now->add(new DateInterval('PT' . $time . 'H'));
                $mission = new Mission();
                $mission->setMissionAt($now);
                $mission->setType(1);
                $mission->setPlanet($planet);
                $mission->setSoldier($soldier);
                $mission->setTank($tank);
                $mission->setWin($percent);
                $mission->setGain($gain * $alea);
                $planet->setSoldier($planet->getSoldier() - $soldier);
                $planet->setTank($planet->getTank() - $tank);
                $em->persist($mission);
                $em->flush();
            }
            return $this->redirectToRoute('zombie', ['usePlanet' => $usePlanet->getId()]);
        }

        return $this->render('connected/zombie.html.twig', [
            'usePlanet' => $usePlanet,
            'planet' => $planet,
            'form_missionZombie' => $form_missionZombie->createView(),
            'form_missionUranium' => $form_missionUranium->createView()
        ]);
    }
}