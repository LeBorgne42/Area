<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialEditFleetType;
use App\Form\Front\FleetRenameType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\Front\FleetRessourcesType;
use App\Form\Front\SpatialFleetType;
use App\Form\Front\FleetSendType;
use App\Form\Front\FleetAttackType;
use App\Form\Front\FleetListType;
use App\Entity\Fleet;
use App\Entity\Report;
use App\Entity\Fleet_List;
use Datetime;
use DatetimeZone;
use DateInterval;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class FleetController  extends AbstractController
{
    /**
     * @Route("/flotte/{idp}", name="fleet", requirements={"idp"="\d+"})
     */
    public function fleetAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGiveMove = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->andWhere('f.planete is not null')
            ->setParameters(['user' => $user])
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        $fleetUsePlanet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->join('p.sector', 's')
            ->where('f.user = :user')
            ->andWhere('f.planete is null')
            ->andWhere('f.planet = :planet')
            ->setParameters(['user' => $user, 'planet' => $usePlanet])
            ->orderBy('s.position, p.position')
            ->getQuery()
            ->getResult();

        $fleetPlanets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->join('p.sector', 's')
            ->where('f.user = :user')
            ->andWhere('f.planete is null')
            ->andWhere('f.planet != :planet')
            ->andWhere('f.planet in (:planets)')
            ->setParameters(['user' => $user, 'planet' => $usePlanet, 'planets' => $user->getPlanets()])
            ->orderBy('s.position, p.position')
            ->getQuery()
            ->getResult();

        $fleetOther = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->join('p.sector', 's')
            ->where('f.user = :user')
            ->andWhere('f.planete is null')
            ->andWhere('f.planet not in (:planets)')
            ->setParameters(['user' => $user, 'planets' => $user->getPlanets()])
            ->orderBy('s.position, p.position')
            ->getQuery()
            ->getResult();

        return $this->render('connected/fleet.html.twig', [
            'date' => $now,
            'usePlanet' => $usePlanet,
            'fleetMove' => $fleetGiveMove,
            'fleetOther' => $fleetOther,
            'fleetPlanets' => $fleetPlanets,
            'fleetUsePlanet' => $fleetUsePlanet,
        ]);
    }

    /**
     * @Route("/flotte-liste/{idp}", name="fleet_list", requirements={"idp"="\d+"})
     */
    public function fleetListAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_listCreate = $this->createForm(FleetListType::class);
        $form_listCreate->handleRequest($request);


        if ($form_listCreate->isSubmitted()) {
            if(count($user->getFleetLists()) >= 10) {
                $this->addFlash("fail", "Vous avez atteint la limite de Cohortes autorisées par l'Instance.");
                return $this->redirectToRoute('fleet_list', ['idp' => $usePlanet->getId()]);
            }

            $fleetList = new Fleet_List();
            $fleetList->setName($form_listCreate->get('name')->getData());
            $fleetList->setPriority($form_listCreate->get('priority')->getData());
            $fleetList->setUser($user);
            $em->persist($fleetList);
            $quest = $user->checkQuests('cohort');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
            $em->flush();
        }

        $fleetLists = $em->getRepository('App:Fleet_List')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy('f.priority')
            ->getQuery()
            ->getResult();

        return $this->render('connected/fleet_list.html.twig', [
            'date' => $now,
            'usePlanet' => $usePlanet,
            'fleetLists' => $fleetLists,
            'form_listCreate' => $form_listCreate->createView()
        ]);
    }

    /**
     * @Route("/flotte-liste-ajouter/{idp}/{fleetList}/{fleet}", name="fleet_list_add", requirements={"idp"="\d+","fleetList"="\d+","fleet"="\d+"})
     */
    public function fleetListAddAction($idp, Fleet_List $fleetList, Fleet $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        if($user == $fleetList->getUser()) {
            $fleetList->addFleet($fleet);
            $fleet->setFleetList($fleetList);
            $em->flush();
        }

        return $this->redirectToRoute('fleet_list', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/flotte-liste-sub/{idp}/{fleetList}/{fleet}", name="fleet_list_sub", requirements={"idp"="\d+","fleetList"="\d+","fleet"="\d+"})
     */
    public function fleetListSubAction($idp, Fleet_List $fleetList, Fleet $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        if($user == $fleetList->getUser()) {
            $fleetList->removeFleet($fleet);
            $fleet->setFleetList(null);
            $em->flush();
        }

        return $this->redirectToRoute('fleet_list', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/flotte-liste-destroy/{idp}/{fleetList}", name="fleet_list_destroy", requirements={"idp"="\d+","fleetList"="\d+"})
     */
    public function fleetListDestroyAction($idp, Fleet_List $fleetList)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        if($user == $fleetList->getUser()) {
            foreach($fleetList->getFleets() as $fleet) {
                $fleetList->removeFleet($fleet);
                $fleet->setFleetList(null);
            }
            $em->remove($fleetList);
            $em->flush();
        }

        return $this->redirectToRoute('fleet_list', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/gerer-flotte/{idp}/{id}", name="manage_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function manageFleetAction(Request $request, $idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        $form_manageFleet = $this->createForm(SpatialEditFleetType::class);
        $form_manageFleet->handleRequest($request);

        $form_manageRenameFleet = $this->createForm(FleetRenameType::class, $fleetGive);
        $form_manageRenameFleet->handleRequest($request);

        $form_manageAttackFleet = $this->createForm(FleetAttackType::class, $fleetGive);
        $form_manageAttackFleet->handleRequest($request);

        $form_sendFleet = $this->createForm(FleetSendType::class, null, ["user" => $user->getId()]);
        $form_sendFleet->handleRequest($request);

        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        if($request->isXmlHttpRequest()) {
            $response = new JsonResponse();
            if ($request->get('name') == 'name') {
                $fleetGive->setName($request->get('data'));
                $em->flush();
                $response->setData(
                    [
                        'has_error' => false,
                    ]
                );
                return $response;
            }
            if ($request->get('name') == 'attack') {
                if ($fleetGive->getMissile() <= 0) {
                    $response->setData(
                        [
                            'has_error' => true,
                        ]
                    );
                    return $response;
                }
                $fleetGive->setAttack($request->get('data'));
                $eAlly = $user->getAllyEnnemy();
                $warAlly = [];
                $x = 0;
                foreach ($eAlly as $tmp) {
                    $warAlly[$x] = $tmp->getAllyTag();
                    $x++;
                }
                $fleetGives = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->join('u.ally', 'a')
                    ->where('f.planet = :planet')
                    ->andWhere('f.attack = true OR a.sigle in (:ally)')
                    ->andWhere('f.user != :user')
                    ->andWhere('f.flightTime is null')
                    ->setParameters(['planet' => $usePlanet, 'ally' => $warAlly, 'user' => $user])
                    ->getQuery()
                    ->getResult();

                if (($fleetGive->getAttack() == true && $planetTake->getFleetNoFriends($user)) || $fleetGives) {
                    $allFleets = $em->getRepository('App:Fleet')
                        ->createQueryBuilder('f')
                        ->join('f.user', 'u')
                        ->where('f.planet = :planet')
                        ->andWhere('f.id != :id')
                        ->andWhere('f.flightTime is null')
                        ->setParameters(['planet' => $planetTake, 'id' => $fleetGive->getId()])
                        ->getQuery()
                        ->getResult();

                    $now = new DateTime();
                    $now->setTimezone(new DateTimeZone('Europe/Paris'));
                    $now->add(new DateInterval('PT' . 300 . 'S'));
                    foreach ($allFleets as $updateF) {
                        $updateF->setFightAt($now);
                    }
                    $fleetGive->setFightAt($now);
                    $em->flush();

                    $response->setData(
                        [
                            'has_error' => false,
                            'war' => true
                        ]
                    );
                    return $response;
                }

                $em->flush();
                $response->setData(
                    [
                        'has_error' => false,
                        'war' => false
                    ]
                );
                return $response;
            }
        }

        if ($form_manageFleet->isSubmitted()) {
            $cargoRessources = $fleetGive->getCargoFull() + abs($form_manageFleet->get('moreNiobium')->getData()) + abs($form_manageFleet->get('moreWater')->getData()) + abs($form_manageFleet->get('moreSoldier')->getData()) + abs($form_manageFleet->get('moreWorker')->getData()) + abs($form_manageFleet->get('moreScientist')->getData());
            if (abs($form_manageFleet->get('moreColonizer')->getData())) {
                $colonizer = $planetTake->getColonizer() - abs($form_manageFleet->get('moreColonizer')->getData());
                $fleetGive->setColonizer($fleetGive->getColonizer() + abs($form_manageFleet->get('moreColonizer')->getData()));
            } elseif (abs($form_manageFleet->get('lessColonizer')->getData()) <= $fleetGive->getColonizer()) {
                $colonizer = $planetTake->getColonizer() + abs($form_manageFleet->get('lessColonizer')->getData());
                $fleetGive->setColonizer($fleetGive->getColonizer() - abs($form_manageFleet->get('lessColonizer')->getData()));
            } else {
                $colonizer = $planetTake->getColonizer();
            }
            if (abs($form_manageFleet->get('moreRecycleur')->getData())) {
                $recycleur = $planetTake->getRecycleur() - abs($form_manageFleet->get('moreRecycleur')->getData());
                $fleetGive->setRecycleur($fleetGive->getRecycleur() + abs($form_manageFleet->get('moreRecycleur')->getData()));
            } elseif (abs($form_manageFleet->get('lessRecycleur')->getData()) <= $fleetGive->getRecycleur()) {
                $recycleur = $planetTake->getRecycleur() + abs($form_manageFleet->get('lessRecycleur')->getData());
                $fleetGive->setRecycleur($fleetGive->getRecycleur() - abs($form_manageFleet->get('lessRecycleur')->getData()));
            } else {
                $recycleur = $planetTake->getRecycleur();
            }
            if (abs($form_manageFleet->get('moreCargoI')->getData())) {
                $cargoI = $planetTake->getCargoI() - abs($form_manageFleet->get('moreCargoI')->getData());
                $fleetGive->setCargoI($fleetGive->getCargoI() + abs($form_manageFleet->get('moreCargoI')->getData()));
            } elseif (abs($form_manageFleet->get('lessCargoI')->getData()) <= $fleetGive->getCargoI()) {
                $cargoI = $planetTake->getCargoI() + abs($form_manageFleet->get('lessCargoI')->getData());
                $fleetGive->setCargoI($fleetGive->getCargoI() - abs($form_manageFleet->get('lessCargoI')->getData()));
            } else {
                $cargoI = $planetTake->getCargoI();
            }
            if (abs($form_manageFleet->get('moreCargoV')->getData())) {
                $cargoV = $planetTake->getCargoV() - abs($form_manageFleet->get('moreCargoV')->getData());
                $fleetGive->setCargoV($fleetGive->getCargoV() + abs($form_manageFleet->get('moreCargoV')->getData()));
            } elseif (abs($form_manageFleet->get('lessCargoV')->getData()) <= $fleetGive->getCargoV()) {
                $cargoV = $planetTake->getCargoV() + abs($form_manageFleet->get('lessCargoV')->getData());
                $fleetGive->setCargoV($fleetGive->getCargoV() - abs($form_manageFleet->get('lessCargoV')->getData()));
            } else {
                $cargoV = $planetTake->getCargoV();
            }
            if (abs($form_manageFleet->get('moreCargoX')->getData())) {
                $cargoX = $planetTake->getCargoX() - abs($form_manageFleet->get('moreCargoX')->getData());
                $fleetGive->setCargoX($fleetGive->getCargoX() + abs($form_manageFleet->get('moreCargoX')->getData()));
            } elseif (abs($form_manageFleet->get('lessCargoX')->getData()) <= $fleetGive->getCargoX()) {
                $cargoX = $planetTake->getCargoX() + abs($form_manageFleet->get('lessCargoX')->getData());
                $fleetGive->setCargoX($fleetGive->getCargoX() - abs($form_manageFleet->get('lessCargoX')->getData()));
            } else {
                $cargoX = $planetTake->getCargoX();
            }
            if (abs($form_manageFleet->get('moreBarge')->getData())) {
                $barge = $planetTake->getBarge() - abs($form_manageFleet->get('moreBarge')->getData());
                $fleetGive->setBarge($fleetGive->getBarge() + abs($form_manageFleet->get('moreBarge')->getData()));
            } elseif (abs($form_manageFleet->get('lessBarge')->getData()) <= $fleetGive->getBarge()) {
                $barge = $planetTake->getBarge() + abs($form_manageFleet->get('lessBarge')->getData());
                $fleetGive->setBarge($fleetGive->getBarge() - abs($form_manageFleet->get('lessBarge')->getData()));
            } else {
                $barge = $planetTake->getBarge();
            }
            if (abs($form_manageFleet->get('moreMoonMaker')->getData())) {
                $moonMaker = $planetTake->getMoonMaker() - abs($form_manageFleet->get('moreMoonMaker')->getData());
                $fleetGive->setMoonMaker($fleetGive->getMoonMaker() + abs($form_manageFleet->get('moreMoonMaker')->getData()));
            } elseif (abs($form_manageFleet->get('lessMoonMaker')->getData()) <= $fleetGive->getMoonMaker()) {
                $moonMaker = $planetTake->getMoonMaker() + abs($form_manageFleet->get('lessMoonMaker')->getData());
                $fleetGive->setMoonMaker($fleetGive->getMoonMaker() - abs($form_manageFleet->get('lessMoonMaker')->getData()));
            } else {
                $moonMaker = $planetTake->getMoonMaker();
            }
            if (abs($form_manageFleet->get('moreRadarShip')->getData())) {
                $radarShip = $planetTake->getRadarShip() - abs($form_manageFleet->get('moreRadarShip')->getData());
                $fleetGive->setRadarShip($fleetGive->getRadarShip() + abs($form_manageFleet->get('moreRadarShip')->getData()));
            } elseif (abs($form_manageFleet->get('lessRadarShip')->getData()) <= $fleetGive->getRadarShip()) {
                $radarShip = $planetTake->getRadarShip() + abs($form_manageFleet->get('lessRadarShip')->getData());
                $fleetGive->setRadarShip($fleetGive->getRadarShip() - abs($form_manageFleet->get('lessRadarShip')->getData()));
            } else {
                $radarShip = $planetTake->getRadarShip();
            }
            if (abs($form_manageFleet->get('moreBrouilleurShip')->getData())) {
                $brouilleurShip = $planetTake->getBrouilleurShip() - abs($form_manageFleet->get('moreBrouilleurShip')->getData());
                $fleetGive->setBrouilleurShip($fleetGive->getBrouilleurShip() + abs($form_manageFleet->get('moreBrouilleurShip')->getData()));
            } elseif (abs($form_manageFleet->get('lessBrouilleurShip')->getData()) <= $fleetGive->getBrouilleurShip()) {
                $brouilleurShip = $planetTake->getBrouilleurShip() + abs($form_manageFleet->get('lessBrouilleurShip')->getData());
                $fleetGive->setBrouilleurShip($fleetGive->getBrouilleurShip() - abs($form_manageFleet->get('lessBrouilleurShip')->getData()));
            } else {
                $brouilleurShip = $planetTake->getBrouilleurShip();
            }
            if (abs($form_manageFleet->get('moreMotherShip')->getData())) {
                $motherShip = $planetTake->getMotherShip() - abs($form_manageFleet->get('moreMotherShip')->getData());
                $fleetGive->setMotherShip($fleetGive->getMotherShip() + abs($form_manageFleet->get('moreMotherShip')->getData()));
            } elseif (abs($form_manageFleet->get('lessMotherShip')->getData()) <= $fleetGive->getMotherShip()) {
                $motherShip = $planetTake->getMotherShip() + abs($form_manageFleet->get('lessMotherShip')->getData());
                $fleetGive->setMotherShip($fleetGive->getMotherShip() - abs($form_manageFleet->get('lessMotherShip')->getData()));
            } else {
                $motherShip = $planetTake->getMotherShip();
            }
            if (abs($form_manageFleet->get('moreSonde')->getData())) {
                $sonde = $planetTake->getSonde() - abs($form_manageFleet->get('moreSonde')->getData());
                $fleetGive->setSonde($fleetGive->getSonde() + abs($form_manageFleet->get('moreSonde')->getData()));
            } elseif (abs($form_manageFleet->get('lessSonde')->getData()) <= $fleetGive->getSonde()) {
                $sonde = $planetTake->getSonde() + abs($form_manageFleet->get('lessSonde')->getData());
                $fleetGive->setSonde($fleetGive->getSonde() - abs($form_manageFleet->get('lessSonde')->getData()));
            } else {
                $sonde = $planetTake->getSonde();
            }
            if (abs($form_manageFleet->get('moreHunter')->getData())) {
                $hunter = $planetTake->getHunter() - abs($form_manageFleet->get('moreHunter')->getData());
                $fleetGive->setHunter($fleetGive->getHunter() + abs($form_manageFleet->get('moreHunter')->getData()));
            } elseif (abs($form_manageFleet->get('lessHunter')->getData()) <= $fleetGive->getHunter()) {
                $hunter = $planetTake->getHunter() + abs($form_manageFleet->get('lessHunter')->getData());
                $fleetGive->setHunter($fleetGive->getHunter() - abs($form_manageFleet->get('lessHunter')->getData()));
            } else {
                $hunter = $planetTake->getHunter();
            }
            if (abs($form_manageFleet->get('moreHunterHeavy')->getData())) {
                $hunterHeavy = $planetTake->getHunterHeavy() - abs($form_manageFleet->get('moreHunterHeavy')->getData());
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() + abs($form_manageFleet->get('moreHunterHeavy')->getData()));
            } elseif (abs($form_manageFleet->get('lessHunterHeavy')->getData()) <= $fleetGive->getHunterHeavy()) {
                $hunterHeavy = $planetTake->getHunterHeavy() + abs($form_manageFleet->get('lessHunterHeavy')->getData());
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() - abs($form_manageFleet->get('lessHunterHeavy')->getData()));
            } else {
                $hunterHeavy = $planetTake->getHunterHeavy();
            }
            if (abs($form_manageFleet->get('moreHunterWar')->getData())) {
                $hunterWar = $planetTake->getHunterWar() - abs($form_manageFleet->get('moreHunterWar')->getData());
                $fleetGive->setHunterWar($fleetGive->getHunterWar() + abs($form_manageFleet->get('moreHunterWar')->getData()));
            } elseif (abs($form_manageFleet->get('lessHunterWar')->getData()) <= $fleetGive->getHunterWar()) {
                $hunterWar = $planetTake->getHunterWar() + abs($form_manageFleet->get('lessHunterWar')->getData());
                $fleetGive->setHunterWar($fleetGive->getHunterWar() - abs($form_manageFleet->get('lessHunterWar')->getData()));
            } else {
                $hunterWar = $planetTake->getHunterWar();
            }
            if (abs($form_manageFleet->get('moreCorvet')->getData())) {
                $corvet = $planetTake->getCorvet() - abs($form_manageFleet->get('moreCorvet')->getData());
                $fleetGive->setCorvet($fleetGive->getCorvet() + abs($form_manageFleet->get('moreCorvet')->getData()));
            } elseif (abs($form_manageFleet->get('lessCorvet')->getData()) <= $fleetGive->getCorvet()) {
                $corvet = $planetTake->getCorvet() + abs($form_manageFleet->get('lessCorvet')->getData());
                $fleetGive->setCorvet($fleetGive->getCorvet() - abs($form_manageFleet->get('lessCorvet')->getData()));
            } else {
                $corvet = $planetTake->getCorvet();
            }
            if (abs($form_manageFleet->get('moreCorvetLaser')->getData())) {
                $corvetLaser = $planetTake->getCorvetLaser() - abs($form_manageFleet->get('moreCorvetLaser')->getData());
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() + abs($form_manageFleet->get('moreCorvetLaser')->getData()));
            } elseif (abs($form_manageFleet->get('lessCorvetLaser')->getData()) <= $fleetGive->getCorvetLaser()) {
                $corvetLaser = $planetTake->getCorvetLaser() + abs($form_manageFleet->get('lessCorvetLaser')->getData());
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() - abs($form_manageFleet->get('lessCorvetLaser')->getData()));
            } else {
                $corvetLaser = $planetTake->getCorvetLaser();
            }
            if (abs($form_manageFleet->get('moreCorvetWar')->getData())) {
                $corvetWar = $planetTake->getCorvetWar() - abs($form_manageFleet->get('moreCorvetWar')->getData());
                $fleetGive->setCorvetWar($fleetGive->getCorvetWar() + abs($form_manageFleet->get('moreCorvetWar')->getData()));
            } elseif (abs($form_manageFleet->get('lessCorvetWar')->getData()) <= $fleetGive->getCorvetWar()) {
                $corvetWar = $planetTake->getCorvetWar() + abs($form_manageFleet->get('lessCorvetWar')->getData());
                $fleetGive->setCorvetWar($fleetGive->getCorvetWar() - abs($form_manageFleet->get('lessCorvetWar')->getData()));
            } else {
                $corvetWar = $planetTake->getCorvetLaser();
            }
            if (abs($form_manageFleet->get('moreFregate')->getData())) {
                $fregate = $planetTake->getFregate() - abs($form_manageFleet->get('moreFregate')->getData());
                $fleetGive->setFregate($fleetGive->getFregate() + abs($form_manageFleet->get('moreFregate')->getData()));
            } elseif (abs($form_manageFleet->get('lessFregate')->getData()) <= $fleetGive->getFregate()) {
                $fregate = $planetTake->getFregate() + abs($form_manageFleet->get('lessFregate')->getData());
                $fleetGive->setFregate($fleetGive->getFregate() - abs($form_manageFleet->get('lessFregate')->getData()));
            } else {
                $fregate = $planetTake->getFregate();
            }
            if (abs($form_manageFleet->get('moreFregatePlasma')->getData())) {
                $fregatePlasma = $planetTake->getFregatePlasma() - abs($form_manageFleet->get('moreFregatePlasma')->getData());
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() + abs($form_manageFleet->get('moreFregatePlasma')->getData()));
            } elseif (abs($form_manageFleet->get('lessFregatePlasma')->getData()) <= $fleetGive->getFregatePlasma()) {
                $fregatePlasma = $planetTake->getFregatePlasma() + abs($form_manageFleet->get('lessFregatePlasma')->getData());
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() - abs($form_manageFleet->get('lessFregatePlasma')->getData()));
            } else {
                $fregatePlasma = $planetTake->getFregatePlasma();
            }
            if (abs($form_manageFleet->get('moreCroiser')->getData())) {
                $croiser = $planetTake->getCroiser() - abs($form_manageFleet->get('moreCroiser')->getData());
                $fleetGive->setCroiser($fleetGive->getCroiser() + abs($form_manageFleet->get('moreCroiser')->getData()));
            } elseif (abs($form_manageFleet->get('lessCroiser')->getData()) <= $fleetGive->getCroiser()) {
                $croiser = $planetTake->getCroiser() + abs($form_manageFleet->get('lessCroiser')->getData());
                $fleetGive->setCroiser($fleetGive->getCroiser() - abs($form_manageFleet->get('lessCroiser')->getData()));
            } else {
                $croiser = $planetTake->getCroiser();
            }
            if (abs($form_manageFleet->get('moreIronClad')->getData())) {
                $ironClad = $planetTake->getIronClad() - abs($form_manageFleet->get('moreIronClad')->getData());
                $fleetGive->setIronClad($fleetGive->getIronClad() + abs($form_manageFleet->get('moreIronClad')->getData()));
            } elseif (abs($form_manageFleet->get('lessIronClad')->getData()) <= $fleetGive->getIronClad()) {
                $ironClad = $planetTake->getIronClad() + abs($form_manageFleet->get('lessIronClad')->getData());
                $fleetGive->setIronClad($fleetGive->getIronClad() - abs($form_manageFleet->get('lessIronClad')->getData()));
            } else {
                $ironClad = $planetTake->getIronClad();
            }
            if (abs($form_manageFleet->get('moreDestroyer')->getData())) {
                $destroyer = $planetTake->getDestroyer() - abs($form_manageFleet->get('moreDestroyer')->getData());
                $fleetGive->setDestroyer($fleetGive->getDestroyer() + abs($form_manageFleet->get('moreDestroyer')->getData()));
            } elseif (abs($form_manageFleet->get('lessDestroyer')->getData()) <= $fleetGive->getDestroyer()) {
                $destroyer = $planetTake->getDestroyer() + abs($form_manageFleet->get('lessDestroyer')->getData());
                $fleetGive->setDestroyer($fleetGive->getDestroyer() - abs($form_manageFleet->get('lessDestroyer')->getData()));
            } else {
                $destroyer = $planetTake->getDestroyer();
            }
            $nbKeep = 0;
            if (abs($form_manageFleet->get('moreNiobium')->getData())) {
                $niobium = $planetTake->getNiobium() - abs($form_manageFleet->get('moreNiobium')->getData());
                $fleetGive->setNiobium($fleetGive->getNiobium() + abs($form_manageFleet->get('moreNiobium')->getData()));
            } elseif (abs($form_manageFleet->get('lessNiobium')->getData()) <= $fleetGive->getNiobium()) {
                $niobium = $planetTake->getNiobium() + abs($form_manageFleet->get('lessNiobium')->getData());
                $fleetGive->setNiobium($fleetGive->getNiobium() - abs($form_manageFleet->get('lessNiobium')->getData()));
            } else {
                $niobium = 0;
                $nbKeep = 1;
            }
            $wtKeep = 0;
            if (abs($form_manageFleet->get('moreWater')->getData())) {
                $water = $planetTake->getWater() - abs($form_manageFleet->get('moreWater')->getData());
                $fleetGive->setWater($fleetGive->getWater() + abs($form_manageFleet->get('moreWater')->getData()));
            } elseif (abs($form_manageFleet->get('lessWater')->getData()) <= $fleetGive->getWater()) {
                $water = $planetTake->getWater() + abs($form_manageFleet->get('lessWater')->getData());
                $fleetGive->setWater($fleetGive->getWater() - abs($form_manageFleet->get('lessWater')->getData()));
            } else {
                $water = 0;
                $wtKeep = 1;
            }
            $solKeep = 0;
            if (abs($form_manageFleet->get('moreSoldier')->getData())) {
                $soldier = $planetTake->getSoldier() - abs($form_manageFleet->get('moreSoldier')->getData());
                $fleetGive->setSoldier($fleetGive->getSoldier() + abs($form_manageFleet->get('moreSoldier')->getData()));
            } elseif (abs($form_manageFleet->get('lessSoldier')->getData()) <= $fleetGive->getSoldier()) {
                $soldier = $planetTake->getSoldier() + abs($form_manageFleet->get('lessSoldier')->getData());
                $fleetGive->setSoldier($fleetGive->getSoldier() - abs($form_manageFleet->get('lessSoldier')->getData()));
            } else {
                $soldier = 0;
                $solKeep = 1;
            }
            $wkKeep = 0;
            if (abs($form_manageFleet->get('moreWorker')->getData())) {
                $worker = $planetTake->getWorker() - abs($form_manageFleet->get('moreWorker')->getData());
                $fleetGive->setWorker($fleetGive->getWorker() + abs($form_manageFleet->get('moreWorker')->getData()));
            } elseif (abs($form_manageFleet->get('lessWorker')->getData()) <= $fleetGive->getWorker()) {
                $worker = $planetTake->getWorker() + abs($form_manageFleet->get('lessWorker')->getData());
                $fleetGive->setWorker($fleetGive->getWorker() - abs($form_manageFleet->get('lessWorker')->getData()));
            } else {
                $worker = 0;
                $wkKeep = 1;
            }
            $scKeep = 0;
            if (abs($form_manageFleet->get('moreScientist')->getData())) {
                $scientist = $planetTake->getScientist() - abs($form_manageFleet->get('moreScientist')->getData());
                $fleetGive->setScientist($fleetGive->getScientist() + abs($form_manageFleet->get('moreScientist')->getData()));
            } elseif (abs($form_manageFleet->get('lessScientist')->getData()) <= $fleetGive->getScientist()) {
                $scientist = $planetTake->getScientist() + abs($form_manageFleet->get('lessScientist')->getData());
                $fleetGive->setScientist($fleetGive->getScientist() - abs($form_manageFleet->get('lessScientist')->getData()));
            } else {
                $scientist = 0;
                $scKeep = 1;
            }
            $cargo = $fleetGive->getCargoPlace() - $cargoRessources;
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($niobium < 0 || $water < 0) || ($soldier < 0 || $worker < 0) || ($scientist < 0 || $cargo < 0) ||
                ($cargoI < 0 || $cargoV < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) ||
                ($destroyer < 0 || $cargoX < 0) || ($hunterWar < 0 || $corvetWar < 0) ||
                ($moonMaker < 0 || $radarShip < 0) || ($brouilleurShip < 0 || $motherShip < 0) ||
                ($soldier > $planetTake->getSoldierMax()) ||
                ($worker > $planetTake->getWorkerMax() || $scientist > $planetTake->getScientistMax()) ||
                ($niobium > $planetTake->getNiobiumMax() || $water > $planetTake->getWaterMax()) ||
                ($worker < 10000 && abs($form_manageFleet->get('moreWorker')->getData()))) {
                return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
            }

            if($fleetGive->getNbrShips() == 0) {
                $em->remove($fleetGive);
            }
            $planetTake->setColonizer($colonizer);
            $planetTake->setCargoI($cargoI);
            $planetTake->setCargoV($cargoV);
            $planetTake->setCargoX($cargoX);
            $planetTake->setRecycleur($recycleur);
            $planetTake->setBarge($barge);
            $planetTake->setMoonMaker($moonMaker);
            $planetTake->setRadarShip($radarShip);
            $planetTake->setBrouilleurShip($brouilleurShip);
            $planetTake->setMotherShip($motherShip);
            $planetTake->setSonde($sonde);
            $planetTake->setHunter($hunter);
            $planetTake->setHunterHeavy($hunterHeavy);
            $planetTake->setHunterWar($hunterWar);
            $planetTake->setCorvet($corvet);
            $planetTake->setCorvetLaser($corvetLaser);
            $planetTake->setCorvetWar($corvetWar);
            $planetTake->setFregate($fregate);
            $planetTake->setFregatePlasma($fregatePlasma);
            $planetTake->setCroiser($croiser);
            $planetTake->setIronClad($ironClad);
            $planetTake->setDestroyer($destroyer);
            if($nbKeep == 0) {
                $planetTake->setNiobium($niobium);
            }
            if($wtKeep == 0) {
                $planetTake->setWater($water);
            }
            if($solKeep == 0) {
                $planetTake->setSoldier($soldier);
            }
            if($wkKeep == 0) {
                $planetTake->setWorker($worker);
            }
            if($scKeep == 0) {
                $planetTake->setScientist($scientist);
            }

            $em->flush();
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/fleet/edit.html.twig', [
            'fleet' => $fleetGive,
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

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet &&
            ($planetTake->getSoldier() + $fleetGive->getSoldier()) <= $planetTake->getSoldierMax() &&
            ($planetTake->getWorker() + $fleetGive->getWorker()) <= $planetTake->getWorkerMax() &&
            ($planetTake->getScientist() + $fleetGive->getScientist()) <= $planetTake->getScientistMax() &&
            ($planetTake->getNiobium() + $fleetGive->getNiobium()) <= $planetTake->getNiobiumMax() &&
            ($planetTake->getWater() + $fleetGive->getWater()) <= $planetTake->getWaterMax()) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        $planetTake->setColonizer($planetTake->getColonizer() + $fleetGive->getColonizer());
        $planetTake->setCargoI($planetTake->getCargoI() + $fleetGive->getCargoI());
        $planetTake->setCargoV($planetTake->getCargoV() + $fleetGive->getCargoV());
        $planetTake->setCargoX($planetTake->getCargoX() + $fleetGive->getCargoX());
        $planetTake->setRecycleur($planetTake->getRecycleur() + $fleetGive->getRecycleur());
        $planetTake->setBarge($planetTake->getBarge() + $fleetGive->getBarge());
        $planetTake->setMoonMaker($planetTake->getMoonMaker() + $fleetGive->getMoonMaker());
        $planetTake->setRadarShip($planetTake->getRadarShip() + $fleetGive->getRadarShip());
        $planetTake->setBrouilleurShip($planetTake->getBrouilleurShip() + $fleetGive->getBrouilleurShip());
        $planetTake->setMotherShip($planetTake->getMotherShip() + $fleetGive->getMotherShip());
        $planetTake->setSonde($planetTake->getSonde() + $fleetGive->getSonde());
        $planetTake->setHunter($planetTake->getHunter() + $fleetGive->getHunter());
        $planetTake->setHunterHeavy($planetTake->getHunterHeavy() + $fleetGive->getHunterHeavy());
        $planetTake->setHunterWar($planetTake->getHunterWar() + $fleetGive->getHunterWar());
        $planetTake->setCorvet($planetTake->getCorvet() + $fleetGive->getCorvet());
        $planetTake->setCorvetLaser($planetTake->getCorvetLaser() + $fleetGive->getCorvetLaser());
        $planetTake->setCorvetWar($planetTake->getCorvetWar() + $fleetGive->getCorvetWar());
        $planetTake->setFregate($planetTake->getFregate() + $fleetGive->getFregate());
        $planetTake->setFregatePlasma($planetTake->getFregatePlasma() + $fleetGive->getFregatePlasma());
        $planetTake->setCroiser($planetTake->getCroiser() + $fleetGive->getCroiser());
        $planetTake->setIronClad($planetTake->getIronClad() + $fleetGive->getIronClad());
        $planetTake->setDestroyer($planetTake->getDestroyer() + $fleetGive->getDestroyer());
        $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
        $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
        $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
        $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
        $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
        $em->remove($fleetGive);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
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
        $moreNow = new DateTime();
        $moreNow->setTimezone(new DateTimeZone('Europe/Paris'));
        $moreNow->add(new DateInterval('PT' . 120 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $form_sendFleet = $this->createForm(FleetSendType::class, null, ["user" => $user->getId()]);
        $form_sendFleet->handleRequest($request);

        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if ($form_sendFleet->isSubmitted() && $form_sendFleet->isValid()) {
            $sectorDestroy = $em->getRepository('App:Sector')
                ->createQueryBuilder('s')
                ->where('s.position = :sector')
                ->andWhere('s.destroy = :true')
                ->setParameters(['sector' => $form_sendFleet->get('sector')->getData(), 'true' => 1])
                ->getQuery()
                ->getOneOrNullResult();

            if($sectorDestroy && $form_sendFleet->get('sector')->getData() != $fleetGive->getPlanet()->getSector()->getPosition()) {
                return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
            }

            if($form_sendFleet->get('planet')->getData()) {
                $planetTake = $form_sendFleet->get('planet')->getData();
                $sector = $planetTake->getSector()->getPosition();
                $planetTakee = $planetTake->getPosition();
                $galaxy = $planetTake->getSector()->getGalaxy()->getPosition();
                if($planetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
                }
            } else {
                if($user->getHyperespace() == 1) {
                    $galaxy = $form_sendFleet->get('galaxy')->getData();
                } else {
                    $galaxy = $fleetGive->getPlanet()->getSector()->getGalaxy()->getPosition();
                }
                $sector = $form_sendFleet->get('sector')->getData();
                $planetTakee = $form_sendFleet->get('planete')->getData();

                if (($galaxy < 1 || $galaxy > 5) || ($sector < 1 || $sector > 100) || ($planetTakee < 1 || $planetTakee > 25)) {
                    return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
                }

                $planetTake = $em->getRepository('App:Planet')
                    ->createQueryBuilder('p')
                    ->join('p.sector', 's')
                    ->join('s.galaxy', 'g')
                    ->where('s.position = :sector')
                    ->andWhere('g.position = :galaxy')
                    ->andWhere('p.position = :planete')
                    ->setParameters(['sector' => $sector, 'galaxy' => $galaxy, 'planete' => $planetTakee])
                    ->getQuery()
                    ->getOneOrNullResult();

                if($planetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
                }
            }
            $sFleet = $fleetGive->getPlanet()->getSector()->getPosition();
            if($fleetGive->getPlanet()->getSector()->getGalaxy()->getPosition() != $galaxy) {
                $base = 100000;
                $price = 25;
            } else {
                $pFleet = $fleetGive->getPlanet()->getPosition();
                $x1 = ($sFleet % 10) * 5 + ($pFleet % 5);
                $x2 = ($sector % 10) * 5 + ($planetTakee % 5);
                $y1 = ($sFleet / 10) * 5 + ($pFleet % 5);
                $y2 = ($sector / 10) * 5 + ($planetTakee % 5);
                $base = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
                $price = $base / 3;
            }
            $carburant = round($price * ($fleetGive->getNbrSignatures() / 200));
            if($carburant > $user->getBitcoin()) {
                return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
            }
            if($fleetGive->getMotherShip()) {
                $speed = $fleetGive->getSpeed() - ($fleetGive->getSpeed() * 0.10);
            } else {
                $speed = $fleetGive->getSpeed();
            }
            $distance = $speed * $base * 500;
            $now->add(new DateInterval('PT' . round($distance) . 'S'));
            $fleetGive->setRecycleAt(null);
            $fleetGive->setNewPlanet($planetTake->getId());
            $fleetGive->setFlightTime($now);
            $fleetGive->setCancelFlight($moreNow);
            if($form_sendFleet->get('flightType')->getData() == '2' && ($planetTake->getUser() || $planetTake->getMerchant() == true)) {
                $fleetGive->setFlightType(2);
                $carburant = $carburant * 2;
            } elseif($form_sendFleet->get('flightType')->getData() == '3' && $planetTake->getUser() == null) {
                $fleetGive->setFlightType(3);
            } elseif($form_sendFleet->get('flightType')->getData() == '4' && $planetTake->getUser()) {
                $fleetGive->setFlightType(4);
            } else {
                $fleetGive->setFlightType(1);
            }
            $fleetGive->setSector($planetTake->getSector());
            $fleetGive->setPlanete($planetTakee);
            $user->setBitcoin($user->getBitcoin() - $carburant);

            $em->flush();
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-niobium/{idp}/{id}", name="discharge_fleet_niobium", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeNiobiumFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        
        if($planetTake->getMerchant() == true) {
            $newWarPointS = ($fleetGive->getNiobium() / 6) / 1000;
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getNiobium() * 0.25)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getNiobium() * 0.25));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setNiobium(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getNiobium() + $fleetGive->getNiobium()) <= $planetTake->getNiobiumMax()) {
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $fleetGive->setNiobium(0);
        } else {
            $planetTake->setNiobium($planetTake->getNiobiumMax());
            $fleetGive->setNiobium(($planetTake->getNiobium() + $fleetGive->getNiobium()) - $planetTake->getNiobiumMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-water/{idp}/{id}", name="discharge_fleet_water", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeWaterFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if($planetTake->getMerchant() == true) {
            $newWarPointS = ($fleetGive->getWater() / 3) / 1000;
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getWater() * 0.5)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWater() * 0.5));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setWater(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getWater() + $fleetGive->getWater()) <= $planetTake->getWaterMax()) {
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $fleetGive->setWater(0);
        } else {
            $planetTake->setWater($planetTake->getWaterMax());
            $fleetGive->setWater(($planetTake->getWater() + $fleetGive->getWater()) - $planetTake->getWaterMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-soldat/{idp}/{id}", name="discharge_fleet_soldier", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeSoldierFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if($planetTake->getMerchant() == true) {
            $newWarPointS = ($fleetGive->getSoldier() * 10) / 1000;
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getSoldier() * 5)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getSoldier() * 5));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setSoldier(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getSoldier() + $fleetGive->getSoldier()) <= $planetTake->getSoldierMax()) {
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $fleetGive->setSoldier(0);
        } else {
            $planetTake->setSoldier($planetTake->getSoldierMax());
            $fleetGive->setSoldier(($planetTake->getSoldier() + $fleetGive->getSoldier()) - $planetTake->getSoldierMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-travailleurs/{idp}/{id}", name="discharge_fleet_worker", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeWorkerFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if($planetTake->getMerchant() == true) {
            $newWarPointS = ($fleetGive->getWorker() * 50) / 1000;
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getWorker() * 2)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWorker() * 2));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setWorker(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getWorker() + $fleetGive->getWorker()) <= $planetTake->getWorkerMax()) {
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $fleetGive->setWorker(0);
        } else {
            $planetTake->setWorker($planetTake->getWorkerMax());
            $fleetGive->setWorker(($planetTake->getWorker() + $fleetGive->getWorker()) - $planetTake->getWorkerMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-scientifique/{idp}/{id}", name="discharge_fleet_scientist", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeScientistFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if($planetTake->getMerchant() == true) {$reportSell = new Report();
            $newWarPointS = ($fleetGive->getScientist() * 100) / 1000;
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getScientist() * 50)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getScientist() * 50));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setScientist(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getScientist() + $fleetGive->getScientist()) <= $planetTake->getScientistMax()) {
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $fleetGive->setScientist(0);
        } else {
            $planetTake->setScientist($planetTake->getScientistMax());
            $fleetGive->setScientist(($planetTake->getScientist() + $fleetGive->getScientist()) - $planetTake->getScientistMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/decharger-tout/{idp}/{id}", name="discharge_fleet_all", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function dischargeAllFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = true')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        if($planetTake->getMerchant() == true) {
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $newWarPointS = ((($fleetGive->getScientist() * 100) + ($fleetGive->getWorker() * 50) + ($fleetGive->getSoldier() * 10) + ($fleetGive->getWater() / 3) + ($fleetGive->getNiobium() / 6)) / 1000);
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . round(($fleetGive->getWater() * 0.5) + ($fleetGive->getSoldier() * 5) + ($fleetGive->getWorker() * 2) + ($fleetGive->getScientist() * 50) + ($fleetGive->getNiobium() * 0.25)) . " bitcoin. Et " . $newWarPointS . " points de Guerre.");
            $em->persist($reportSell);
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getScientist() * 50) + ($fleetGive->getWorker() * 2) + ($fleetGive->getSoldier() * 5) + ($fleetGive->getWater() * 0.5) + ($fleetGive->getNiobium() * 0.25));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $fleetGive->setScientist(0);
            $fleetGive->setNiobium(0);
            $fleetGive->setSoldier(0);
            $fleetGive->setWorker(0);
            $fleetGive->setWater(0);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 500);
            }
        }
        if(($planetTake->getScientist() + $fleetGive->getScientist()) <= $planetTake->getScientistMax() &&
            ($planetTake->getWorker() + $fleetGive->getWorker()) <= $planetTake->getWorkerMax() &&
            ($planetTake->getSoldier() + $fleetGive->getSoldier()) <= $planetTake->getSoldierMax() &&
            ($planetTake->getWater() + $fleetGive->getWater()) <= $planetTake->getWaterMax() &&
            ($planetTake->getNiobium() + $fleetGive->getNiobium()) <= $planetTake->getNiobiumMax()) {
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $fleetGive->setNiobium(0);
            $fleetGive->setWater(0);
            $fleetGive->setSoldier(0);
            $fleetGive->setWorker(0);
            $fleetGive->setScientist(0);
        } else {
            $planetTake->setScientist($planetTake->getScientistMax());
            $fleetGive->setScientist(($planetTake->getScientist() + $fleetGive->getScientist()) - $planetTake->getScientistMax());
            $planetTake->setNiobium($planetTake->getNiobiumMax());
            $fleetGive->setNiobium(($planetTake->getNiobium() + $fleetGive->getNiobium()) - $planetTake->getNiobiumMax());
            $planetTake->setWater($planetTake->getWaterMax());
            $fleetGive->setWater(($planetTake->getWater() + $fleetGive->getWater()) - $planetTake->getWaterMax());
            $planetTake->setSoldier($planetTake->getSoldierMax());
            $fleetGive->setSoldier(($planetTake->getSoldier() + $fleetGive->getSoldier()) - $planetTake->getSoldierMax());
            $planetTake->setWorker($planetTake->getWorkerMax());
            $fleetGive->setWorker(($planetTake->getWorker() + $fleetGive->getWorker()) - $planetTake->getWorkerMax());
        }
        $server->setNbrSell($server->getNbrSell() + 1);

        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/fusionner-flotte/{idp}/{id}/{id2}", name="fusion_fleet", requirements={"idp"="\d+", "id"="\d+", "id2"="\d+"})
     */
    public function fusionFleetAction($idp, $id, $id2)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id2, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        $fleetTake->setColonizer($fleetTake->getColonizer() + $fleetGive->getColonizer());
        $fleetTake->setCargoI($fleetTake->getCargoI() + $fleetGive->getCargoI());
        $fleetTake->setCargoV($fleetTake->getCargoV() + $fleetGive->getCargoV());
        $fleetTake->setCargoX($fleetTake->getCargoX() + $fleetGive->getCargoX());
        $fleetTake->setRecycleur($fleetTake->getRecycleur() + $fleetGive->getRecycleur());
        $fleetTake->setBarge($fleetTake->getBarge() + $fleetGive->getBarge());
        $fleetTake->setMoonMaker($fleetTake->getMoonMaker() + $fleetGive->getMoonMaker());
        $fleetTake->setRadarShip($fleetTake->getRadarShip() + $fleetGive->getRadarShip());
        $fleetTake->setBrouilleurShip($fleetTake->getBrouilleurShip() + $fleetGive->getBrouilleurShip());
        $fleetTake->setMotherShip($fleetTake->getMotherShip() + $fleetGive->getMotherShip());
        $fleetTake->setSonde($fleetTake->getSonde() + $fleetGive->getSonde());
        $fleetTake->setHunter($fleetTake->getHunter() + $fleetGive->getHunter());
        $fleetTake->setHunterHeavy($fleetTake->getHunterHeavy() + $fleetGive->getHunterHeavy());
        $fleetTake->setHunterWar($fleetTake->getHunterWar() + $fleetGive->getHunterWar());
        $fleetTake->setCorvet($fleetTake->getCorvet() + $fleetGive->getCorvet());
        $fleetTake->setCorvetLaser($fleetTake->getCorvetLaser() + $fleetGive->getCorvetLaser());
        $fleetTake->setCorvetWar($fleetTake->getCorvetWar() + $fleetGive->getCorvetWar());
        $fleetTake->setFregate($fleetTake->getFregate() + $fleetGive->getFregate());
        $fleetTake->setFregatePlasma($fleetTake->getFregatePlasma() + $fleetGive->getFregatePlasma());
        $fleetTake->setCroiser($fleetTake->getCroiser() + $fleetGive->getCroiser());
        $fleetTake->setIronClad($fleetTake->getIronClad() + $fleetGive->getIronClad());
        $fleetTake->setDestroyer($fleetTake->getDestroyer() + $fleetGive->getDestroyer());
        $fleetTake->setNiobium($fleetTake->getNiobium() + $fleetGive->getNiobium());
        $fleetTake->setWater($fleetTake->getWater() + $fleetGive->getWater());
        $fleetTake->setSoldier($fleetTake->getSoldier() + $fleetGive->getSoldier());
        $fleetTake->setWorker($fleetTake->getWorker() + $fleetGive->getWorker());
        $fleetTake->setScientist($fleetTake->getScientist() + $fleetGive->getScientist());
        $em->remove($fleetGive);
        $fleetTake->setRecycleAt(null);
        $em->flush();

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/recycler-flotte/{idp}/{id}/", name="recycle_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function recycleFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        if($fleet && $usePlanet &&
            ($fleet->getPlanet()->getNbCdr() > 0 || $fleet->getPlanet()->getWtCdr() > 0) &&
            $fleet->getRecycleur() && $fleet->getCargoPlace() > $fleet->getCargoFull()) {
        } else {
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $fleet->setRecycleAt($now);

        $em->flush();

        return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $fleet->getPlanet()->getSector()->getPosition(), 'gal' => $fleet->getPlanet()->getSector()->getGalaxy()->getPosition()]);
    }

    /**
     * @Route("/annuler-recycler-flotte/{idp}/{id}/", name="cancel_recycle_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function cancelRecycleFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $fleet->setRecycleAt(null);

        $em->flush();

        return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $fleet->getPlanet()->getSector()->getPosition(), 'gal' => $fleet->getPlanet()->getSector()->getGalaxy()->getPosition()]);
    }

    /**
     * @Route("/scinder-flotte/{idp}/{id}", name="fleet_split", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function splitFleetAction(Request $request, $idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if(count($user->getFleets()) >= 75) {
            $this->addFlash("fail", "Vous avez atteint la limite de flottes autorisées par l'Instance.");
            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        $oldFleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $form_spatialShip = $this->createForm(SpatialFleetType::class);
        $form_spatialShip->handleRequest($request);

        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            $cargoI = $oldFleet->getCargoI() - abs($form_spatialShip->get('cargoI')->getData());
            $cargoV = $oldFleet->getCargoV() - abs($form_spatialShip->get('cargoV')->getData());
            $cargoX = $oldFleet->getCargoX() - abs($form_spatialShip->get('cargoX')->getData());
            $colonizer = $oldFleet->getColonizer() - abs($form_spatialShip->get('colonizer')->getData());
            $recycleur = $oldFleet->getRecycleur() - abs($form_spatialShip->get('recycleur')->getData());
            $barge = $oldFleet->getBarge() - abs($form_spatialShip->get('barge')->getData());
            $moonMaker = $oldFleet->getMoonMaker() - abs($form_spatialShip->get('moonMaker')->getData());
            $radarShip = $oldFleet->getRadarShip() - abs($form_spatialShip->get('radarShip')->getData());
            $brouilleurShip = $oldFleet->getBrouilleurShip() - abs($form_spatialShip->get('brouilleurShip')->getData());
            $motherShip = $oldFleet->getMotherShip() - abs($form_spatialShip->get('motherShip')->getData());
            $sonde = $oldFleet->getSonde() - abs($form_spatialShip->get('sonde')->getData());
            $hunter = $oldFleet->getHunter() - abs($form_spatialShip->get('hunter')->getData());
            $fregate = $oldFleet->getFregate() - abs($form_spatialShip->get('fregate')->getData());
            $hunterHeavy = $oldFleet->getHunterHeavy() - abs($form_spatialShip->get('hunterHeavy')->getData());
            $hunterWar = $oldFleet->getHunterWar() - abs($form_spatialShip->get('hunterWar')->getData());
            $corvet = $oldFleet->getCorvet() - abs($form_spatialShip->get('corvet')->getData());
            $corvetLaser = $oldFleet->getCorvetLaser() - abs($form_spatialShip->get('corvetLaser')->getData());
            $corvetWar = $oldFleet->getCorvetWar() - abs($form_spatialShip->get('corvetWar')->getData());
            $fregatePlasma = $oldFleet->getFregatePlasma() - abs($form_spatialShip->get('fregatePlasma')->getData());
            $croiser = $oldFleet->getCroiser() - abs($form_spatialShip->get('croiser')->getData());
            $ironClad = $oldFleet->getIronClad() - abs($form_spatialShip->get('ironClad')->getData());
            $destroyer = $oldFleet->getDestroyer() - abs($form_spatialShip->get('destroyer')->getData());
            $total = $form_spatialShip->get('moonMaker')->getData() + $form_spatialShip->get('radarShip')->getData() + $form_spatialShip->get('brouilleurShip')->getData() + $form_spatialShip->get('motherShip')->getData() + $form_spatialShip->get('corvetWar')->getData() + $form_spatialShip->get('hunterWar')->getData() + $form_spatialShip->get('cargoI')->getData() + $form_spatialShip->get('cargoV')->getData() + $form_spatialShip->get('cargoX')->getData() + $form_spatialShip->get('hunterHeavy')->getData() + $form_spatialShip->get('corvet')->getData() + $form_spatialShip->get('corvetLaser')->getData() + $form_spatialShip->get('fregatePlasma')->getData() + $form_spatialShip->get('croiser')->getData() + $form_spatialShip->get('ironClad')->getData() + $form_spatialShip->get('destroyer')->getData() + $form_spatialShip->get('colonizer')->getData() + $form_spatialShip->get('fregate')->getData() + $form_spatialShip->get('hunter')->getData() + $form_spatialShip->get('sonde')->getData() + $form_spatialShip->get('barge')->getData() + $form_spatialShip->get('recycleur')->getData();
            $cargoTotal = (($cargoI * 25000) + ($cargoV * 100000) + ($cargoX * 250000) + ($barge * 2500) + ($recycleur * 10000)) - $oldFleet->getCargoFull();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($total == 0 || $cargoI < 0) || ($cargoV < 0 || $cargoX < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) || ($destroyer < 0 || $hunterWar < 0) ||
                ($corvetWar < 0 || $moonMaker < 0) || ($radarShip < 0 || $brouilleurShip < 0) || ($motherShip < 0 || $cargoTotal < 0)) {
                return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
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
                ->leftJoin('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.attack = true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->andWhere('f.flightTime is null')
                ->setParameters(['planet' => $oldFleet->getPlanet(), 'ally' => $warAlly, 'user' => $user])
                ->getQuery()
                ->getResult();

            $fleet = new Fleet();
            $fleet->setCargoI($form_spatialShip->get('cargoI')->getData());
            $fleet->setCargoV($form_spatialShip->get('cargoV')->getData());
            $fleet->setCargoX($form_spatialShip->get('cargoX')->getData());
            $fleet->setColonizer($form_spatialShip->get('colonizer')->getData());
            $fleet->setRecycleur($form_spatialShip->get('recycleur')->getData());
            $fleet->setBarge($form_spatialShip->get('barge')->getData());
            $fleet->setMoonMaker($form_spatialShip->get('moonMaker')->getData());
            $fleet->setRadarShip($form_spatialShip->get('radarShip')->getData());
            $fleet->setBrouilleurShip($form_spatialShip->get('brouilleurShip')->getData());
            $fleet->setMotherShip($form_spatialShip->get('motherShip')->getData());
            $fleet->setSonde($form_spatialShip->get('sonde')->getData());
            $fleet->setHunter($form_spatialShip->get('hunter')->getData());
            $fleet->setFregate($form_spatialShip->get('fregate')->getData());
            $fleet->setHunterHeavy($form_spatialShip->get('hunterHeavy')->getData());
            $fleet->setHunterWar($form_spatialShip->get('hunterWar')->getData());
            $fleet->setCorvet($form_spatialShip->get('corvet')->getData());
            $fleet->setCorvetLaser($form_spatialShip->get('corvetLaser')->getData());
            $fleet->setCorvetWar($form_spatialShip->get('corvetWar')->getData());
            $fleet->setFregatePlasma($form_spatialShip->get('fregatePlasma')->getData());
            $fleet->setCroiser($form_spatialShip->get('croiser')->getData());
            $fleet->setIronClad($form_spatialShip->get('ironClad')->getData());
            $fleet->setDestroyer($form_spatialShip->get('destroyer')->getData());
            if($fleets) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.user != :user')
                    ->setParameters(['planet' => $oldFleet->getPlanet()])
                    ->getQuery()
                    ->getResult();
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));
                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                }
                $fleet->setFightAt($now);
            }
            $fleet->setUser($user);
            $fleet->setPlanet($oldFleet->getPlanet());
            $fleet->setName($form_spatialShip->get('name')->getData());
            $em->persist($fleet);
            $oldFleet->setCargoI($cargoI);
            $oldFleet->setCargoV($cargoV);
            $oldFleet->setCargoX($cargoX);
            $oldFleet->setColonizer($colonizer);
            $oldFleet->setRecycleur($recycleur);
            $oldFleet->setBarge($barge);
            $oldFleet->setMoonMaker($moonMaker);
            $oldFleet->setRadarShip($radarShip);
            $oldFleet->setBrouilleurShip($brouilleurShip);
            $oldFleet->setMotherShip($motherShip);
            $oldFleet->setSonde($sonde);
            $oldFleet->setHunter($hunter);
            $oldFleet->setFregate($fregate);
            $oldFleet->setHunterHeavy($hunterHeavy);
            $oldFleet->setHunterWar($hunterWar);
            $oldFleet->setCorvet($corvet);
            $oldFleet->setCorvetLaser($corvetLaser);
            $oldFleet->setCorvetWar($corvetWar);
            $oldFleet->setFregatePlasma($fregatePlasma);
            $oldFleet->setCroiser($croiser);
            $oldFleet->setIronClad($ironClad);
            $oldFleet->setDestroyer($destroyer);

            $em->flush();


            return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/fleet/split.html.twig', [
            'usePlanet' => $usePlanet,
            'oldFleet' => $oldFleet,
            'form_spatialShip' => $form_spatialShip->createView(),
        ]);
    }

    /**
     * @Route("/annuler-flotte/{idp}/{id}/", name="cancel_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function cancelFleetAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        if($fleet->getCancelFlight() > $now) {
            $fleet->setFlightTime(null);
            $fleet->setPlanete(null);
            $fleet->setSector(null);
            $fleet->setNewPlanet(null);

            $em->flush();
        }

        return $this->redirectToRoute('fleet', ['idp' => $usePlanet->getId()]);
    }
}