<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialEditFleetType;
use App\Form\Front\FleetRenameType;
use App\Form\Front\FleetRessourcesType;
use App\Form\Front\FleetSendType;
use App\Form\Front\FleetAttackType;
use Datetime;
use DatetimeZone;
use DateInterval;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class FleetController extends Controller
{
    /**
     * @Route("/flotte/{idp}", name="fleet", requirements={"idp"="\d+"})
     */
    public function fleetAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetMove = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->andWhere('f.planete is not null')
            ->setParameters(array('user' => $user))
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        return $this->render('connected/fleet.html.twig', [
            'usePlanet' => $usePlanet,
            'fleetMove' => $fleetMove,
        ]);
    }

    /**
     * @Route("/gerer-flotte/{idp}/{id}", name="manage_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function manageFleetAction(Request $request, $idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        $form_manageFleet = $this->createForm(SpatialEditFleetType::class);
        $form_manageFleet->handleRequest($request);

        $form_manageRenameFleet = $this->createForm(FleetRenameType::class, $fleet);
        $form_manageRenameFleet->handleRequest($request);

        $form_manageAttackFleet = $this->createForm(FleetAttackType::class, $fleet);
        $form_manageAttackFleet->handleRequest($request);

        $form_sendFleet = $this->createForm(FleetSendType::class);
        $form_sendFleet->handleRequest($request);

        if(($fleet || $usePlanet) && ($fleet->getFightAt() == null && $fleet->getFlightTime() == null) && $fleet->getUser() == $user) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageRenameFleet->isSubmitted()) {
            $em->persist($fleet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageAttackFleet->isSubmitted()) {
            if($fleet->getMissile() <= 0) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
            $eAlly = $user->getAllyEnnemy();
            $warAlly = [];
            $x = 0;
            foreach ($eAlly as $tmp) {
                $warAlly[$x] = $tmp->getAllyTag();
                $x++;
            }
            $fleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.user', 'u')
                ->join('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.attack = :true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->setParameters(array('planet' => $usePlanet, 'true' => true, 'ally' => $warAlly, 'user' => $user))
                ->getQuery()
                ->getResult();

            if(($fleet->getAttack() == true && $planet->getFleetNoFriends($user)) || $fleets) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.user != :user')
                    ->setParameters(array('planet' => $planet, 'user' => $user))
                    ->getQuery()
                    ->getResult();
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));
                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                    $em->persist($updateF);
                }
                $fleet->setFightAt($now);
            }
            $em->persist($fleet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageFleet->isSubmitted()) {
            if ($form_manageFleet->get('moreColonizer')->getData()) {
                $colonizer = $planet->getColonizer() - $form_manageFleet->get('moreColonizer')->getData();
                $fleet->setColonizer($fleet->getColonizer() + $form_manageFleet->get('moreColonizer')->getData());
            } elseif ($form_manageFleet->get('lessColonizer')->getData() <= $fleet->getColonizer()) {
                $colonizer = $planet->getColonizer() + $form_manageFleet->get('lessColonizer')->getData();
                $fleet->setColonizer($fleet->getColonizer() - $form_manageFleet->get('lessColonizer')->getData());
            } else {
                $colonizer = $planet->getColonizer();
            }
            if ($form_manageFleet->get('moreRecycleur')->getData()) {
                $recycleur = $planet->getRecycleur() - $form_manageFleet->get('moreRecycleur')->getData();
                $fleet->setRecycleur($fleet->getRecycleur() + $form_manageFleet->get('moreRecycleur')->getData());
            } elseif ($form_manageFleet->get('lessRecycleur')->getData() <= $fleet->getRecycleur()) {
                $recycleur = $planet->getRecycleur() + $form_manageFleet->get('lessRecycleur')->getData();
                $fleet->setRecycleur($fleet->getRecycleur() - $form_manageFleet->get('lessRecycleur')->getData());
            } else {
                $recycleur = $planet->getRecycleur();
            }
            if ($form_manageFleet->get('moreBarge')->getData()) {
                $barge = $planet->getBarge() - $form_manageFleet->get('moreBarge')->getData();
                $fleet->setBarge($fleet->getBarge() + $form_manageFleet->get('moreBarge')->getData());
            } elseif ($form_manageFleet->get('lessBarge')->getData() <= $fleet->getBarge()) {
                $barge = $planet->getBarge() + $form_manageFleet->get('lessBarge')->getData();
                $fleet->setBarge($fleet->getBarge() - $form_manageFleet->get('lessBarge')->getData());
            } else {
                $barge = $planet->getBarge();
            }
            if ($form_manageFleet->get('moreSonde')->getData()) {
                $sonde = $planet->getSonde() - $form_manageFleet->get('moreSonde')->getData();
                $fleet->setSonde($fleet->getSonde() + $form_manageFleet->get('moreSonde')->getData());
            } elseif ($form_manageFleet->get('lessSonde')->getData() <= $fleet->getSonde()) {
                $sonde = $planet->getSonde() + $form_manageFleet->get('lessSonde')->getData();
                $fleet->setSonde($fleet->getSonde() - $form_manageFleet->get('lessSonde')->getData());
            } else {
                $sonde = $planet->getSonde();
            }
            if ($form_manageFleet->get('moreHunter')->getData()) {
                $hunter = $planet->getHunter() - $form_manageFleet->get('moreHunter')->getData();
                $fleet->setHunter($fleet->getHunter() + $form_manageFleet->get('moreHunter')->getData());
            } elseif ($form_manageFleet->get('lessHunter')->getData() <= $fleet->getHunter()) {
                $hunter = $planet->getHunter() + $form_manageFleet->get('lessHunter')->getData();
                $fleet->setHunter($fleet->getHunter() - $form_manageFleet->get('lessHunter')->getData());
            } else {
                $hunter = $planet->getHunter();
            }
            if ($form_manageFleet->get('moreFregate')->getData()) {
                $fregate = $planet->getFregate() - $form_manageFleet->get('moreFregate')->getData();
                $fleet->setFregate($fleet->getFregate() + $form_manageFleet->get('moreFregate')->getData());
            } elseif ($form_manageFleet->get('lessFregate')->getData() <= $fleet->getFregate()) {
                $fregate = $planet->getFregate() + $form_manageFleet->get('lessFregate')->getData();
                $fleet->setFregate($fleet->getFregate() - $form_manageFleet->get('lessFregate')->getData());
            } else {
                $fregate = $planet->getFregate();
            }
            if ($form_manageFleet->get('moreNiobium')->getData()) {
                $niobium = $planet->getNiobium() - $form_manageFleet->get('moreNiobium')->getData();
                $fleet->setNiobium($fleet->getNiobium() + $form_manageFleet->get('moreNiobium')->getData());
            } elseif ($form_manageFleet->get('lessNiobium')->getData() <= $fleet->getNiobium()) {
                $niobium = $planet->getNiobium() + $form_manageFleet->get('lessNiobium')->getData();
                $fleet->setNiobium($fleet->getNiobium() - $form_manageFleet->get('lessNiobium')->getData());
            } else {
                $niobium = $planet->getNiobium();
            }
            if ($form_manageFleet->get('moreWater')->getData()) {
                $water = $planet->getWater() - $form_manageFleet->get('moreWater')->getData();
                $fleet->setWater($fleet->getWater() + $form_manageFleet->get('moreWater')->getData());
            } elseif ($form_manageFleet->get('lessWater')->getData() <= $fleet->getWater()) {
                $water = $planet->getWater() + $form_manageFleet->get('lessWater')->getData();
                $fleet->setWater($fleet->getWater() - $form_manageFleet->get('lessWater')->getData());
            } else {
                $water = $planet->getWater();
            }
            if ($form_manageFleet->get('moreSoldier')->getData()) {
                $soldier = $planet->getSoldier() - $form_manageFleet->get('moreSoldier')->getData();
                $fleet->setSoldier($fleet->getSoldier() + $form_manageFleet->get('moreSoldier')->getData());
            } elseif ($form_manageFleet->get('lessSoldier')->getData() <= $fleet->getSoldier()) {
                $soldier = $planet->getSoldier() + $form_manageFleet->get('lessSoldier')->getData();
                $fleet->setSoldier($fleet->getSoldier() - $form_manageFleet->get('lessSoldier')->getData());
            } else {
                $soldier = $planet->getSoldier();
            }
            if ($form_manageFleet->get('moreWorker')->getData()) {
                $worker = $planet->getWorker() - $form_manageFleet->get('moreWorker')->getData();
                $fleet->setWorker($fleet->getWorker() + $form_manageFleet->get('moreWorker')->getData());
            } elseif ($form_manageFleet->get('lessWorker')->getData() <= $fleet->getWorker()) {
                $worker = $planet->getWorker() + $form_manageFleet->get('lessWorker')->getData();
                $fleet->setWorker($fleet->getWorker() - $form_manageFleet->get('lessWorker')->getData());
            } else {
                $worker = $planet->getWorker();
            }
            if ($form_manageFleet->get('moreScientist')->getData()) {
                $scientist = $planet->getScientist() - $form_manageFleet->get('moreScientist')->getData();
                $fleet->setScientist($fleet->getScientist() + $form_manageFleet->get('moreScientist')->getData());
            } elseif ($form_manageFleet->get('lessScientist')->getData() <= $fleet->getScientist()) {
                $scientist = $planet->getScientist() + $form_manageFleet->get('lessScientist')->getData();
                $fleet->setScientist($fleet->getScientist() - $form_manageFleet->get('lessScientist')->getData());
            } else {
                $scientist = $planet->getScientist();
            }
            $cargo = ($fleet->getCargoPlace() - $fleet->getCargoFull()) - $niobium + $water + $soldier + $worker + $scientist;
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($niobium < 0 || $water < 0) || ($soldier < 0 || $worker < 0) || ($scientist < 0 || $cargo < 0)) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            } else {
                $em->persist($fleet);
            }
            $planet->setColonizer($colonizer);
            $planet->setRecycleur($recycleur);
            $planet->setBarge($barge);
            $planet->setSonde($sonde);
            $planet->setHunter($hunter);
            $planet->setFregate($fregate);
            $planet->setNiobium($niobium);
            $planet->setWater($water);
            $planet->setSoldier($soldier);
            $planet->setWorker($worker);
            $planet->setScientist($scientist);
            $em->persist($planet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/fleet/edit.html.twig', [
            'fleet' => $fleet,
            'usePlanet' => $usePlanet,
            'form_manageFleet' => $form_manageFleet->createView(),
            'form_sendFleet' => $form_sendFleet->createView(),
            'form_manageRenameFleet' => $form_manageRenameFleet->createView(),
            'form_manageAttackFleet' => $form_manageAttackFleet->createView(),
        ]);
    }

    /**
     * @Route("/detruire-flotte/{idp}/{id}", name="destroy_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function destroyFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planet->setColonizer($planet->getColonizer() + $fleet->getColonizer());
        $planet->setRecycleur($planet->getRecycleur() + $fleet->getRecycleur());
        $planet->setBarge($planet->getBarge() + $fleet->getBarge());
        $planet->setSonde($planet->getSonde() + $fleet->getSonde());
        $planet->setHunter($planet->getHunter() + $fleet->getHunter());
        $planet->setFregate($planet->getFregate() + $fleet->getFregate());
        $planet->setNiobium($planet->getNiobium() + $fleet->getNiobium());
        $planet->setWater($planet->getWater() + $fleet->getWater());
        $planet->setSoldier($planet->getSoldier() + $fleet->getSoldier());
        $planet->setWorker($planet->getWorker() + $fleet->getWorker());
        $planet->setScientist($planet->getScientist() + $fleet->getScientist());
        $em->remove($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/envoyer-flotte/{idp}/{id}", name="send_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function sendFleetAction(Request $request, $idp, $id)
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

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_sendFleet = $this->createForm(FleetSendType::class);
        $form_sendFleet->handleRequest($request);

        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_sendFleet->isSubmitted() && $form_sendFleet->isValid()) {
            $galaxy = 1;
            $sector= $form_sendFleet->get('sector')->getData();
            $planete= $form_sendFleet->get('planete')->getData();

            if (($galaxy < 1 || $galaxy > 10) || ($sector < 1 || $sector > 100) || ($planete < 1 || $planete > 25) ||
                ($galaxy != 1 && $user->getHyperespace() == 0)) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            $planet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.sector', 's')
                ->where('s.position = :sector')
                ->andWhere('s.galaxy = :galaxy')
                ->andWhere('p.position = :planete')
                ->setParameters(array('sector' => $sector, 'galaxy' => $galaxy, 'planete' => $planete))
                ->getQuery()
                ->getOneOrNullResult();

            $sFleet= $fleet->getPlanet()->getSector()->getPosition();
            if (strpos('0 -1 1 -10 10 -9 9', (strval($sFleet - $sector)) ) != false) {
                $base= 3000;
            } elseif (strpos('-20 20 12 11 8 2 -12 -11 -8 -2', (strval($sFleet - $sector)) ) != false) {
                $base= 6800;
            } elseif (strpos('-28 28 29 30 31 32 33 22 12 3 7 -29 -30 -31 -32 -33 -22 -13 -3 -7', (strval($sFleet - $sector)) ) != false) {
                $base= 8000;
            } else {
                $base= 15000;
            }
            $now->add(new DateInterval('PT' . ($fleet->getSpeed() * $base) . 'S'));
            $fleet->setNewPlanet($planet->getId());
            $fleet->setFlightTime($now);
            $fleet->setSector($planet->getSector());
            $fleet->setPlanete($planete);
            $em->persist($fleet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/decharger-niobium/{idp}/{id}", name="discharge_fleet_niobium", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeNiobiumFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if($planet->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleet->getNiobium()));
        }
        $planet->setNiobium($planet->getNiobium() + $fleet->getNiobium());
        $fleet->setNiobium(0);
        $em->persist($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/decharger-water/{idp}/{id}", name="discharge_fleet_water", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeWaterFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planet->setWater($planet->getWater() + $fleet->getWater());
        if($planet->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleet->getWater() / 1.5));
        }
        $fleet->setWater(0);
        $em->persist($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/decharger-soldat/{idp}/{id}", name="discharge_fleet_soldier", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeSoldierFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planet->setSoldier($planet->getSoldier() + $fleet->getSoldier());
        if($planet->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleet->getSoldier() * 7.5));
        }
        $fleet->setSoldier(0);
        $em->persist($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/decharger-travailleurs/{idp}/{id}", name="discharge_fleet_worker", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeWorkerFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planet->setWorker($planet->getWorker() + $fleet->getWorker());
        if($planet->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleet->getWorker() / 4));
        }
        $fleet->setWorker(0);
        $em->persist($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/decharger-scientifique/{idp}/{id}", name="discharge_fleet_scientist", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeScientistFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planet->setScientist($planet->getScientist() + $fleet->getScientist());
        if($planet->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleet->getScientist() * 75));
        }
        $fleet->setScientist(0);
        $em->persist($fleet);
        $em->persist($planet);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }
}