<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialEditFleetType;
use App\Form\Front\FleetRenameType;
use App\Form\Front\FleetRessourcesType;
use App\Form\Front\SpatialFleetType;
use App\Form\Front\FleetSendType;
use App\Form\Front\FleetAttackType;
use App\Entity\Fleet;
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

        $fleetGiveMove = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->andWhere('f.planete is not null')
            ->setParameters(array('user' => $user))
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        return $this->render('connected/fleet.html.twig', [
            'usePlanet' => $usePlanet,
            'fleetMove' => $fleetGiveMove,
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        $form_manageFleet = $this->createForm(SpatialEditFleetType::class);
        $form_manageFleet->handleRequest($request);

        $form_manageRenameFleet = $this->createForm(FleetRenameType::class, $fleetGive);
        $form_manageRenameFleet->handleRequest($request);

        $form_manageAttackFleet = $this->createForm(FleetAttackType::class, $fleetGive);
        $form_manageAttackFleet->handleRequest($request);

        $form_sendFleet = $this->createForm(FleetSendType::class, null, array("user" => $user->getId()));
        $form_sendFleet->handleRequest($request);

        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageRenameFleet->isSubmitted()) {
            $em->persist($fleetGive);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageAttackFleet->isSubmitted()) {
            if($fleetGive->getMissile() <= 0) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
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
                ->andWhere('f.attack = :true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->andWhere('f.flightTime is null')
                ->setParameters(array('planet' => $usePlanet, 'true' => true, 'ally' => $warAlly, 'user' => $user))
                ->getQuery()
                ->getResult();

            if(($fleetGive->getAttack() == true && $planetTake->getFleetNoFriends($user)) || $fleetGives) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.user != :user')
                    ->andWhere('f.flightTime is null')
                    ->setParameters(array('planet' => $planetTake, 'user' => $user))
                    ->getQuery()
                    ->getResult();
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $now->add(new DateInterval('PT' . 300 . 'S'));
                foreach ($allFleets as $updateF) {
                    $updateF->setFightAt($now);
                    $em->persist($updateF);
                }
                $fleetGive->setFightAt($now);
            }
            $em->persist($fleetGive);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageFleet->isSubmitted()) {
            $cargoRessources = $fleetGive->getCargoFull() + $form_manageFleet->get('moreNiobium')->getData() + $form_manageFleet->get('moreWater')->getData() + $form_manageFleet->get('moreSoldier')->getData() + $form_manageFleet->get('moreWorker')->getData() + $form_manageFleet->get('moreScientist')->getData();
            if ($form_manageFleet->get('moreColonizer')->getData()) {
                $colonizer = $planetTake->getColonizer() - $form_manageFleet->get('moreColonizer')->getData();
                $fleetGive->setColonizer($fleetGive->getColonizer() + $form_manageFleet->get('moreColonizer')->getData());
            } elseif ($form_manageFleet->get('lessColonizer')->getData() <= $fleetGive->getColonizer()) {
                $colonizer = $planetTake->getColonizer() + $form_manageFleet->get('lessColonizer')->getData();
                $fleetGive->setColonizer($fleetGive->getColonizer() - $form_manageFleet->get('lessColonizer')->getData());
            } else {
                $colonizer = $planetTake->getColonizer();
            }
            if ($form_manageFleet->get('moreRecycleur')->getData()) {
                $recycleur = $planetTake->getRecycleur() - $form_manageFleet->get('moreRecycleur')->getData();
                $fleetGive->setRecycleur($fleetGive->getRecycleur() + $form_manageFleet->get('moreRecycleur')->getData());
            } elseif ($form_manageFleet->get('lessRecycleur')->getData() <= $fleetGive->getRecycleur()) {
                $recycleur = $planetTake->getRecycleur() + $form_manageFleet->get('lessRecycleur')->getData();
                $fleetGive->setRecycleur($fleetGive->getRecycleur() - $form_manageFleet->get('lessRecycleur')->getData());
            } else {
                $recycleur = $planetTake->getRecycleur();
            }
            if ($form_manageFleet->get('moreCargoI')->getData()) {
                $cargoI = $planetTake->getCargoI() - $form_manageFleet->get('moreCargoI')->getData();
                $fleetGive->setCargoI($fleetGive->getCargoI() + $form_manageFleet->get('moreCargoI')->getData());
            } elseif ($form_manageFleet->get('lessCargoI')->getData() <= $fleetGive->getCargoI()) {
                $cargoI = $planetTake->getCargoI() + $form_manageFleet->get('lessCargoI')->getData();
                $fleetGive->setCargoI($fleetGive->getCargoI() - $form_manageFleet->get('lessCargoI')->getData());
            } else {
                $cargoI = $planetTake->getCargoI();
            }
            if ($form_manageFleet->get('moreCargoV')->getData()) {
                $cargoV = $planetTake->getCargoV() - $form_manageFleet->get('moreCargoV')->getData();
                $fleetGive->setCargoV($fleetGive->getCargoV() + $form_manageFleet->get('moreCargoV')->getData());
            } elseif ($form_manageFleet->get('lessCargoV')->getData() <= $fleetGive->getCargoV()) {
                $cargoV = $planetTake->getCargoV() + $form_manageFleet->get('lessCargoV')->getData();
                $fleetGive->setCargoV($fleetGive->getCargoV() - $form_manageFleet->get('lessCargoV')->getData());
            } else {
                $cargoV = $planetTake->getCargoV();
            }
            if ($form_manageFleet->get('moreCargoX')->getData()) {
                $cargoX = $planetTake->getCargoX() - $form_manageFleet->get('moreCargoX')->getData();
                $fleetGive->setCargoX($fleetGive->getCargoX() + $form_manageFleet->get('moreCargoX')->getData());
            } elseif ($form_manageFleet->get('lessCargoX')->getData() <= $fleetGive->getCargoX()) {
                $cargoX = $planetTake->getCargoX() + $form_manageFleet->get('lessCargoX')->getData();
                $fleetGive->setCargoX($fleetGive->getCargoX() - $form_manageFleet->get('lessCargoX')->getData());
            } else {
                $cargoX = $planetTake->getCargoX();
            }
            if ($form_manageFleet->get('moreBarge')->getData()) {
                $barge = $planetTake->getBarge() - $form_manageFleet->get('moreBarge')->getData();
                $fleetGive->setBarge($fleetGive->getBarge() + $form_manageFleet->get('moreBarge')->getData());
            } elseif ($form_manageFleet->get('lessBarge')->getData() <= $fleetGive->getBarge()) {
                $barge = $planetTake->getBarge() + $form_manageFleet->get('lessBarge')->getData();
                $fleetGive->setBarge($fleetGive->getBarge() - $form_manageFleet->get('lessBarge')->getData());
            } else {
                $barge = $planetTake->getBarge();
            }
            if ($form_manageFleet->get('moreSonde')->getData()) {
                $sonde = $planetTake->getSonde() - $form_manageFleet->get('moreSonde')->getData();
                $fleetGive->setSonde($fleetGive->getSonde() + $form_manageFleet->get('moreSonde')->getData());
            } elseif ($form_manageFleet->get('lessSonde')->getData() <= $fleetGive->getSonde()) {
                $sonde = $planetTake->getSonde() + $form_manageFleet->get('lessSonde')->getData();
                $fleetGive->setSonde($fleetGive->getSonde() - $form_manageFleet->get('lessSonde')->getData());
            } else {
                $sonde = $planetTake->getSonde();
            }
            if ($form_manageFleet->get('moreHunter')->getData()) {
                $hunter = $planetTake->getHunter() - $form_manageFleet->get('moreHunter')->getData();
                $fleetGive->setHunter($fleetGive->getHunter() + $form_manageFleet->get('moreHunter')->getData());
            } elseif ($form_manageFleet->get('lessHunter')->getData() <= $fleetGive->getHunter()) {
                $hunter = $planetTake->getHunter() + $form_manageFleet->get('lessHunter')->getData();
                $fleetGive->setHunter($fleetGive->getHunter() - $form_manageFleet->get('lessHunter')->getData());
            } else {
                $hunter = $planetTake->getHunter();
            }
            if ($form_manageFleet->get('moreHunterHeavy')->getData()) {
                $hunterHeavy = $planetTake->getHunterHeavy() - $form_manageFleet->get('moreHunterHeavy')->getData();
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() + $form_manageFleet->get('moreHunterHeavy')->getData());
            } elseif ($form_manageFleet->get('lessHunterHeavy')->getData() <= $fleetGive->getHunterHeavy()) {
                $hunterHeavy = $planetTake->getHunterHeavy() + $form_manageFleet->get('lessHunterHeavy')->getData();
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() - $form_manageFleet->get('lessHunterHeavy')->getData());
            } else {
                $hunterHeavy = $planetTake->getHunterHeavy();
            }
            if ($form_manageFleet->get('moreHunterWar')->getData()) {
                $hunterWar = $planetTake->getHunterWar() - $form_manageFleet->get('moreHunterWar')->getData();
                $fleetGive->setHunterWar($fleetGive->getHunterWar() + $form_manageFleet->get('moreHunterWar')->getData());
            } elseif ($form_manageFleet->get('lessHunterWar')->getData() <= $fleetGive->getHunterWar()) {
                $hunterWar = $planetTake->getHunterWar() + $form_manageFleet->get('lessHunterWar')->getData();
                $fleetGive->setHunterWar($fleetGive->getHunterWar() - $form_manageFleet->get('lessHunterWar')->getData());
            } else {
                $hunterWar = $planetTake->getHunterWar();
            }
            if ($form_manageFleet->get('moreCorvet')->getData()) {
                $corvet = $planetTake->getCorvet() - $form_manageFleet->get('moreCorvet')->getData();
                $fleetGive->setCorvet($fleetGive->getCorvet() + $form_manageFleet->get('moreCorvet')->getData());
            } elseif ($form_manageFleet->get('lessCorvet')->getData() <= $fleetGive->getCorvet()) {
                $corvet = $planetTake->getCorvet() + $form_manageFleet->get('lessCorvet')->getData();
                $fleetGive->setCorvet($fleetGive->getCorvet() - $form_manageFleet->get('lessCorvet')->getData());
            } else {
                $corvet = $planetTake->getCorvet();
            }
            if ($form_manageFleet->get('moreCorvetLaser')->getData()) {
                $corvetLaser = $planetTake->getCorvetLaser() - $form_manageFleet->get('moreCorvetLaser')->getData();
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() + $form_manageFleet->get('moreCorvetLaser')->getData());
            } elseif ($form_manageFleet->get('lessCorvetLaser')->getData() <= $fleetGive->getCorvetLaser()) {
                $corvetLaser = $planetTake->getCorvetLaser() + $form_manageFleet->get('lessCorvetLaser')->getData();
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() - $form_manageFleet->get('lessCorvetLaser')->getData());
            } else {
                $corvetLaser = $planetTake->getCorvetLaser();
            }
            if ($form_manageFleet->get('moreCorvetWar')->getData()) {
                $corvetWar = $planetTake->getCorvetWar() - $form_manageFleet->get('moreCorvetWar')->getData();
                $fleetGive->setCorvetWar($fleetGive->getCorvetWar() + $form_manageFleet->get('moreCorvetWar')->getData());
            } elseif ($form_manageFleet->get('lessCorvetWar')->getData() <= $fleetGive->getCorvetWar()) {
                $corvetWar = $planetTake->getCorvetWar() + $form_manageFleet->get('lessCorvetWar')->getData();
                $fleetGive->setCorvetWar($fleetGive->getCorvetWar() - $form_manageFleet->get('lessCorvetWar')->getData());
            } else {
                $corvetWar = $planetTake->getCorvetLaser();
            }
            if ($form_manageFleet->get('moreFregate')->getData()) {
                $fregate = $planetTake->getFregate() - $form_manageFleet->get('moreFregate')->getData();
                $fleetGive->setFregate($fleetGive->getFregate() + $form_manageFleet->get('moreFregate')->getData());
            } elseif ($form_manageFleet->get('lessFregate')->getData() <= $fleetGive->getFregate()) {
                $fregate = $planetTake->getFregate() + $form_manageFleet->get('lessFregate')->getData();
                $fleetGive->setFregate($fleetGive->getFregate() - $form_manageFleet->get('lessFregate')->getData());
            } else {
                $fregate = $planetTake->getFregate();
            }
            if ($form_manageFleet->get('moreFregatePlasma')->getData()) {
                $fregatePlasma = $planetTake->getFregatePlasma() - $form_manageFleet->get('moreFregatePlasma')->getData();
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() + $form_manageFleet->get('moreFregatePlasma')->getData());
            } elseif ($form_manageFleet->get('lessFregatePlasma')->getData() <= $fleetGive->getFregatePlasma()) {
                $fregatePlasma = $planetTake->getFregatePlasma() + $form_manageFleet->get('lessFregatePlasma')->getData();
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() - $form_manageFleet->get('lessFregatePlasma')->getData());
            } else {
                $fregatePlasma = $planetTake->getFregatePlasma();
            }
            if ($form_manageFleet->get('moreCroiser')->getData()) {
                $croiser = $planetTake->getCroiser() - $form_manageFleet->get('moreCroiser')->getData();
                $fleetGive->setCroiser($fleetGive->getCroiser() + $form_manageFleet->get('moreCroiser')->getData());
            } elseif ($form_manageFleet->get('lessCroiser')->getData() <= $fleetGive->getCroiser()) {
                $croiser = $planetTake->getCroiser() + $form_manageFleet->get('lessCroiser')->getData();
                $fleetGive->setCroiser($fleetGive->getCroiser() - $form_manageFleet->get('lessCroiser')->getData());
            } else {
                $croiser = $planetTake->getCroiser();
            }
            if ($form_manageFleet->get('moreIronClad')->getData()) {
                $ironClad = $planetTake->getIronClad() - $form_manageFleet->get('moreIronClad')->getData();
                $fleetGive->setIronClad($fleetGive->getIronClad() + $form_manageFleet->get('moreIronClad')->getData());
            } elseif ($form_manageFleet->get('lessIronClad')->getData() <= $fleetGive->getIronClad()) {
                $ironClad = $planetTake->getIronClad() + $form_manageFleet->get('lessIronClad')->getData();
                $fleetGive->setIronClad($fleetGive->getIronClad() - $form_manageFleet->get('lessIronClad')->getData());
            } else {
                $ironClad = $planetTake->getIronClad();
            }
            if ($form_manageFleet->get('moreDestroyer')->getData()) {
                $destroyer = $planetTake->getDestroyer() - $form_manageFleet->get('moreDestroyer')->getData();
                $fleetGive->setDestroyer($fleetGive->getDestroyer() + $form_manageFleet->get('moreDestroyer')->getData());
            } elseif ($form_manageFleet->get('lessDestroyer')->getData() <= $fleetGive->getDestroyer()) {
                $destroyer = $planetTake->getDestroyer() + $form_manageFleet->get('lessDestroyer')->getData();
                $fleetGive->setDestroyer($fleetGive->getDestroyer() - $form_manageFleet->get('lessDestroyer')->getData());
            } else {
                $destroyer = $planetTake->getDestroyer();
            }
            $nbKeep = 0;
            if ($form_manageFleet->get('moreNiobium')->getData()) {
                $niobium = $planetTake->getNiobium() - $form_manageFleet->get('moreNiobium')->getData();
                $fleetGive->setNiobium($fleetGive->getNiobium() + $form_manageFleet->get('moreNiobium')->getData());
            } elseif ($form_manageFleet->get('lessNiobium')->getData() <= $fleetGive->getNiobium()) {
                $niobium = $planetTake->getNiobium() + $form_manageFleet->get('lessNiobium')->getData();
                $fleetGive->setNiobium($fleetGive->getNiobium() - $form_manageFleet->get('lessNiobium')->getData());
            } else {
                $niobium = 0;
                $nbKeep = 1;
            }
            $wtKeep = 0;
            if ($form_manageFleet->get('moreWater')->getData()) {
                $water = $planetTake->getWater() - $form_manageFleet->get('moreWater')->getData();
                $fleetGive->setWater($fleetGive->getWater() + $form_manageFleet->get('moreWater')->getData());
            } elseif ($form_manageFleet->get('lessWater')->getData() <= $fleetGive->getWater()) {
                $water = $planetTake->getWater() + $form_manageFleet->get('lessWater')->getData();
                $fleetGive->setWater($fleetGive->getWater() - $form_manageFleet->get('lessWater')->getData());
            } else {
                $water = 0;
                $wtKeep = 1;
            }
            $solKeep = 0;
            if ($form_manageFleet->get('moreSoldier')->getData()) {
                $soldier = $planetTake->getSoldier() - $form_manageFleet->get('moreSoldier')->getData();
                $fleetGive->setSoldier($fleetGive->getSoldier() + $form_manageFleet->get('moreSoldier')->getData());
            } elseif ($form_manageFleet->get('lessSoldier')->getData() <= $fleetGive->getSoldier()) {
                $soldier = $planetTake->getSoldier() + $form_manageFleet->get('lessSoldier')->getData();
                $fleetGive->setSoldier($fleetGive->getSoldier() - $form_manageFleet->get('lessSoldier')->getData());
            } else {
                $soldier = 0;
                $solKeep = 1;
            }
            $wkKeep = 0;
            if ($form_manageFleet->get('moreWorker')->getData()) {
                $worker = $planetTake->getWorker() - $form_manageFleet->get('moreWorker')->getData();
                $fleetGive->setWorker($fleetGive->getWorker() + $form_manageFleet->get('moreWorker')->getData());
            } elseif ($form_manageFleet->get('lessWorker')->getData() <= $fleetGive->getWorker()) {
                $worker = $planetTake->getWorker() + $form_manageFleet->get('lessWorker')->getData();
                $fleetGive->setWorker($fleetGive->getWorker() - $form_manageFleet->get('lessWorker')->getData());
            } else {
                $worker = 0;
                $wkKeep = 1;
            }
            $scKeep = 0;
            if ($form_manageFleet->get('moreScientist')->getData()) {
                $scientist = $planetTake->getScientist() - $form_manageFleet->get('moreScientist')->getData();
                $fleetGive->setScientist($fleetGive->getScientist() + $form_manageFleet->get('moreScientist')->getData());
            } elseif ($form_manageFleet->get('lessScientist')->getData() <= $fleetGive->getScientist()) {
                $scientist = $planetTake->getScientist() + $form_manageFleet->get('lessScientist')->getData();
                $fleetGive->setScientist($fleetGive->getScientist() - $form_manageFleet->get('lessScientist')->getData());
            } else {
                $scientist = 0;
                $scKeep = 1;
            }
            $cargo = $fleetGive->getCargoPlace() - $cargoRessources;
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($niobium < 0 || $water < 0) || ($soldier < 0 || $worker < 0) || ($scientist < 0 || $cargo < 0) ||
                ($cargoI < 0 || $cargoV < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) ||
                ($destroyer < 0 || $cargoX < 0) || ($hunterWar < 0 || $corvetWar < 0) || ($soldier > $planetTake->getSoldierMax()) ||
                ($worker > $planetTake->getWorkerMax() || $scientist > $planetTake->getScientistMax()) ||
                ($niobium > $planetTake->getNiobiumMax() || $water > $planetTake->getWaterMax())) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            if($fleetGive->getNbrShips() == 0) {
                $em->remove($fleetGive);
            } else {
                $em->persist($fleetGive);
            }
            $planetTake->setColonizer($colonizer);
            $planetTake->setCargoI($cargoI);
            $planetTake->setCargoV($cargoV);
            $planetTake->setCargoX($cargoX);
            $planetTake->setRecycleur($recycleur);
            $planetTake->setBarge($barge);
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

            $em->persist($planetTake);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if(!$fleetGive && !$usePlanet &&
            ($planetTake->getSoldier() + $fleetGive->getSoldier()) <= $planetTake->getSoldierMax() &&
            ($planetTake->getWorker() + $fleetGive->getWorker()) <= $planetTake->getWorkerMax() &&
            ($planetTake->getScientist() + $fleetGive->getScientist()) <= $planetTake->getScientistMax() &&
            ($planetTake->getNiobium() + $fleetGive->getNiobium()) <= $planetTake->getNiobiumMax() &&
            ($planetTake->getWater() + $fleetGive->getWater()) <= $planetTake->getWaterMax()) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $planetTake->setColonizer($planetTake->getColonizer() + $fleetGive->getColonizer());
        $planetTake->setCargoI($planetTake->getCargoI() + $fleetGive->getCargoI());
        $planetTake->setCargoV($planetTake->getCargoV() + $fleetGive->getCargoV());
        $planetTake->setCargoX($planetTake->getCargoX() + $fleetGive->getCargoX());
        $planetTake->setRecycleur($planetTake->getRecycleur() + $fleetGive->getRecycleur());
        $planetTake->setBarge($planetTake->getBarge() + $fleetGive->getBarge());
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
        $em->persist($planetTake);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_sendFleet = $this->createForm(FleetSendType::class, null, array("user" => $user->getId()));
        $form_sendFleet->handleRequest($request);

        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_sendFleet->isSubmitted() && $form_sendFleet->isValid()) {
            if($form_sendFleet->get('planet')->getData()) {
                $planetTake = $form_sendFleet->get('planet')->getData();
                $sector = $planetTake->getSector()->getPosition();
                $planetTakee = $planetTake->getPosition();
                if($planetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }
            } else {
                $galaxy = 1;
                $sector = $form_sendFleet->get('sector')->getData();
                $planetTakee = $form_sendFleet->get('planete')->getData();

                if (($galaxy < 1 || $galaxy > 10) || ($sector < 1 || $sector > 100) || ($planetTakee < 1 || $planetTakee > 25) ||
                    ($galaxy != 1 && $user->getHyperespace() == 0)) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }

                $planetTake = $em->getRepository('App:Planet')
                    ->createQueryBuilder('p')
                    ->join('p.sector', 's')
                    ->where('s.position = :sector')
                    ->andWhere('s.galaxy = :galaxy')
                    ->andWhere('p.position = :planete')
                    ->setParameters(array('sector' => $sector, 'galaxy' => $galaxy, 'planete' => $planetTakee))
                    ->getQuery()
                    ->getOneOrNullResult();
                if($planetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }
            }

            $sFleet= $fleetGive->getPlanet()->getSector()->getPosition();
            if ($sFleet == $sector) {
                $pFleet = $fleetGive->getPlanet()->getPosition();
                if (strpos('0 -1 1 -4 4 -5 5 6 -6', (strval($pFleet - $planetTakee)) ) != false) {
                    $base = 1500;
                    $price = 0.7;
                } elseif (strpos('2 -2 3 -3 7 -7 8 -8 9 -9 10 -10 11 -11 12 -12', (strval($pFleet - $planetTakee)) ) != false) {
                    $base = 1750;
                    $price = 0.9;
                } else {
                    $base = 2000;
                    $price = 1;
                }
            } elseif (strpos('0 -1 1 -10 10 -9 9', (strval($sFleet - $sector)) ) != false) {
                $base = 3000;
                $price = 1.5;
            } elseif (strpos('-20 20 12 11 8 2 -12 -11 -8 -2', (strval($sFleet - $sector)) ) != false) {
                $base = 6800;
                $price = 3.4;
            } elseif (strpos('-28 28 29 30 31 32 33 22 12 3 7 -29 -30 -31 -32 -33 -22 -13 -3 -7', (strval($sFleet - $sector)) ) != false) {
                $base = 8000;
                $price = 4;
            } else {
                $base = 12000;
                $price = 6;
            }
            $carburant = round($price * ($fleetGive->getNbrSignatures() / 200));
            if($carburant > $user->getBitcoin()) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
            $now->add(new DateInterval('PT' . round($fleetGive->getSpeed() * $base) . 'S'));
            $fleetGive->setRecycleAt(null);
            $fleetGive->setNewPlanet($planetTake->getId());
            $fleetGive->setFlightTime($now);
            $fleetGive->setFlightType($form_sendFleet->get('flightType')->getData());
            $fleetGive->setSector($planetTake->getSector());
            $fleetGive->setPlanete($planetTakee);
            $user->setBitcoin($user->getBitcoin() - $carburant);
            $em->persist($fleetGive);
            $em->persist($user);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = :true')
            ->setParameters(array('id' => $id, 'user' => $user, 'true' => true))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        
        if($planetTake->getMerchant() == true) {
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getNiobium() / 1.5));
            $fleetGive->setNiobium(0);
        }
        if(($planetTake->getNiobium() + $fleetGive->getNiobium()) <= $planetTake->getNiobiumMax()) {
            $planetTake->setNiobium($planetTake->getNiobium() + $fleetGive->getNiobium());
            $fleetGive->setNiobium(0);
        } else {
            $planetTake->setNiobium($planetTake->getNiobiumMax());
            $fleetGive->setNiobium(($planetTake->getNiobium() + $fleetGive->getNiobium()) - $planetTake->getNiobiumMax());
        }
        $em->persist($fleetGive);
        $em->persist($planetTake);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = :true')
            ->setParameters(array('id' => $id, 'user' => $user, 'true' => true))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if($planetTake->getMerchant() == true) {
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWater() * 2));
            $fleetGive->setWater(0);
        }
        if(($planetTake->getWater() + $fleetGive->getWater()) <= $planetTake->getWaterMax()) {
            $planetTake->setWater($planetTake->getWater() + $fleetGive->getWater());
            $fleetGive->setWater(0);
        } else {
            $planetTake->setWater($planetTake->getWaterMax());
            $fleetGive->setWater(($planetTake->getWater() + $fleetGive->getWater()) - $planetTake->getWaterMax());
        }
        $em->persist($fleetGive);
        $em->persist($planetTake);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = :true')
            ->setParameters(array('id' => $id, 'user' => $user, 'true' => true))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if($planetTake->getMerchant() == true) {
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getSoldier() * 7.5));
            $fleetGive->setSoldier(0);
        }
        if(($planetTake->getSoldier() + $fleetGive->getSoldier()) <= $planetTake->getSoldierMax()) {
            $planetTake->setSoldier($planetTake->getSoldier() + $fleetGive->getSoldier());
            $fleetGive->setSoldier(0);
        } else {
            $planetTake->setSoldier($planetTake->getSoldierMax());
            $fleetGive->setSoldier(($planetTake->getSoldier() + $fleetGive->getSoldier()) - $planetTake->getSoldierMax());
        }
        $em->persist($fleetGive);
        $em->persist($planetTake);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = :true')
            ->setParameters(array('id' => $id, 'user' => $user, 'true' => true))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if($planetTake->getMerchant() == true) {
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWorker() * 2));
            $fleetGive->setWorker(0);
        }
        if(($planetTake->getWorker() + $fleetGive->getWorker()) <= $planetTake->getWorkerMax()) {
            $planetTake->setWorker($planetTake->getWorker() + $fleetGive->getWorker());
            $fleetGive->setWorker(0);
        } else {
            $planetTake->setWorker($planetTake->getWorkerMax());
            $fleetGive->setWorker(($planetTake->getWorker() + $fleetGive->getWorker()) - $planetTake->getWorkerMax());
        }
        $em->persist($fleetGive);
        $em->persist($planetTake);
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->andWhere('p.user is not null or p.merchant = :true')
            ->setParameters(array('id' => $id, 'user' => $user, 'true' => true))
            ->getQuery()
            ->getOneOrNullResult();

        $planetTake = $fleetGive->getPlanet();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if($planetTake->getMerchant() == true) {
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getScientist() * 75));
            $fleetGive->setScientist(0);
        }
        if(($planetTake->getScientist() + $fleetGive->getScientist()) <= $planetTake->getScientistMax()) {
            $planetTake->setScientist($planetTake->getScientist() + $fleetGive->getScientist());
            $fleetGive->setScientist(0);
        } else {
            $planetTake->setScientist($planetTake->getScientistMax());
            $fleetGive->setScientist(($planetTake->getScientist() + $fleetGive->getScientist()) - $planetTake->getScientistMax());
        }
        $em->persist($fleetGive);
        $em->persist($planetTake);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/fusionner-flotte/{idp}/{id}/{id2}", name="fusion_fleet", requirements={"idp"="\d+", "id"="\d+", "id2"="\d+"})
     */
    public function fusionFleetAction($idp, $id, $id2)
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

        $fleetGive = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id2, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();
        if($fleetGive && $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setColonizer($fleetTake->getColonizer() + $fleetGive->getColonizer());
        $fleetTake->setCargoI($fleetTake->getCargoI() + $fleetGive->getCargoI());
        $fleetTake->setCargoV($fleetTake->getCargoV() + $fleetGive->getCargoV());
        $fleetTake->setCargoX($fleetTake->getCargoX() + $fleetGive->getCargoX());
        $fleetTake->setRecycleur($fleetTake->getRecycleur() + $fleetGive->getRecycleur());
        $fleetTake->setBarge($fleetTake->getBarge() + $fleetGive->getBarge());
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
        $em->persist($fleetTake);
        $em->flush();

        return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
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
            ->andWhere('f.fightAt is null')
            ->andWhere('f.flightTime is null')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        if($fleet && $usePlanet &&
            ($fleet->getPlanet()->getNbCdr() > 0 || $fleet->getPlanet()->getWtCdr() > 0) &&
            $fleet->getRecycleur() && $fleet->getCargoPlace() > $fleet->getCargoFull()) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . 3600 . 'S'));
        $fleet->setRecycleAt($now);
        $em->persist($fleet);
        $em->flush();

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $fleet->getPlanet()->getSector()->getPosition()));
    }

    /**
     * @Route("/annuler-recycler-flotte/{idp}/{id}/", name="cancel_recycle_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function cancelRecycleFleetAction($idp, $id)
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

        $fleet->setRecycleAt(null);
        $em->persist($fleet);
        $em->flush();

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $fleet->getPlanet()->getSector()->getPosition()));
    }

    /**
     * @Route("/scinder-flotte/{idp}/{id}", name="fleet_split", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function splitFleetAction(Request $request, $idp, $id)
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

        $oldFleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_spatialShip = $this->createForm(SpatialFleetType::class);
        $form_spatialShip->handleRequest($request);

        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            $cargoI = $oldFleet->getCargoI() - $form_spatialShip->get('cargoI')->getData();
            $cargoV = $oldFleet->getCargoV() - $form_spatialShip->get('cargoV')->getData();
            $cargoX = $oldFleet->getCargoX() - $form_spatialShip->get('cargoX')->getData();
            $colonizer = $oldFleet->getColonizer() - $form_spatialShip->get('colonizer')->getData();
            $recycleur = $oldFleet->getRecycleur() - $form_spatialShip->get('recycleur')->getData();
            $barge = $oldFleet->getBarge() - $form_spatialShip->get('barge')->getData();
            $sonde = $oldFleet->getSonde() - $form_spatialShip->get('sonde')->getData();
            $hunter = $oldFleet->getHunter() - $form_spatialShip->get('hunter')->getData();
            $fregate = $oldFleet->getFregate() - $form_spatialShip->get('fregate')->getData();
            $hunterHeavy = $oldFleet->getHunterHeavy() - $form_spatialShip->get('hunterHeavy')->getData();
            $hunterWar = $oldFleet->getHunterWar() - $form_spatialShip->get('hunterWar')->getData();
            $corvet = $oldFleet->getCorvet() - $form_spatialShip->get('corvet')->getData();
            $corvetLaser = $oldFleet->getCorvetLaser() - $form_spatialShip->get('corvetLaser')->getData();
            $corvetWar = $oldFleet->getCorvetWar() - $form_spatialShip->get('corvetWar')->getData();
            $fregatePlasma = $oldFleet->getFregatePlasma() - $form_spatialShip->get('fregatePlasma')->getData();
            $croiser = $oldFleet->getCroiser() - $form_spatialShip->get('croiser')->getData();
            $ironClad = $oldFleet->getIronClad() - $form_spatialShip->get('ironClad')->getData();
            $destroyer = $oldFleet->getDestroyer() - $form_spatialShip->get('destroyer')->getData();
            $total = $form_spatialShip->get('corvetWar')->getData() + $form_spatialShip->get('hunterWar')->getData() + $form_spatialShip->get('cargoI')->getData() + $form_spatialShip->get('cargoV')->getData() + $form_spatialShip->get('cargoX')->getData() + $form_spatialShip->get('hunterHeavy')->getData() + $form_spatialShip->get('corvet')->getData() + $form_spatialShip->get('corvetLaser')->getData() + $form_spatialShip->get('fregatePlasma')->getData() + $form_spatialShip->get('croiser')->getData() + $form_spatialShip->get('ironClad')->getData() + $form_spatialShip->get('destroyer')->getData() + $form_spatialShip->get('colonizer')->getData() + $form_spatialShip->get('fregate')->getData() + $form_spatialShip->get('hunter')->getData() + $form_spatialShip->get('sonde')->getData() + $form_spatialShip->get('barge')->getData() + $form_spatialShip->get('recycleur')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($total == 0 || $cargoI < 0) || ($cargoV < 0 || $cargoX < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) || ($destroyer < 0 || $hunterWar < 0) ||
                ($corvetWar < 0)) {
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
                ->leftJoin('u.ally', 'a')
                ->where('f.planet = :planet')
                ->andWhere('f.attack = :true OR a.sigle in (:ally)')
                ->andWhere('f.user != :user')
                ->andWhere('f.flightTime is null')
                ->setParameters(array('planet' => $oldFleet->getPlanet(), 'true' => true, 'ally' => $warAlly, 'user' => $user))
                ->getQuery()
                ->getResult();

            $fleet = new Fleet();
            $fleet->setCargoI($form_spatialShip->get('cargoI')->getData());
            $fleet->setCargoV($form_spatialShip->get('cargoV')->getData());
            $fleet->setCargoX($form_spatialShip->get('cargoX')->getData());
            $fleet->setColonizer($form_spatialShip->get('colonizer')->getData());
            $fleet->setRecycleur($form_spatialShip->get('recycleur')->getData());
            $fleet->setBarge($form_spatialShip->get('barge')->getData());
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
                    ->setParameters(array('planet' => $oldFleet->getPlanet(), 'user' => $user))
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
            $em->persist($oldFleet);
            $em->flush();


            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/fleet/split.html.twig', [
            'usePlanet' => $usePlanet,
            'oldFleet' => $oldFleet,
            'form_spatialShip' => $form_spatialShip->createView(),
        ]);
    }
}