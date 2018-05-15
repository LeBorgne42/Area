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
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        $form_manageFleet = $this->createForm(SpatialEditFleetType::class);
        $form_manageFleet->handleRequest($request);

        $form_manageRenameFleet = $this->createForm(FleetRenameType::class, $fleetGive);
        $form_manageRenameFleet->handleRequest($request);

        $form_manageAttackFleet = $this->createForm(FleetAttackType::class, $fleetGive);
        $form_manageAttackFleet->handleRequest($request);

        $form_sendFleet = $this->createForm(FleetSendType::class, null, array("user" => $user->getId()));
        $form_sendFleet->handleRequest($request);

        if(($fleetGive || $usePlanet) && ($fleetGive->getFightAt() == null && $fleetGive->getFlightTime() == null) && $fleetGive->getUser() == $user) {
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
                ->setParameters(array('planet' => $usePlanet, 'true' => true, 'ally' => $warAlly, 'user' => $user))
                ->getQuery()
                ->getResult();

            if(($fleetGive->getAttack() == true && $fleetTake->getFleetNoFriends($user)) || $fleetGives) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.user != :user')
                    ->setParameters(array('planet' => $fleetTake, 'user' => $user))
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
                $colonizer = $fleetTake->getColonizer() - $form_manageFleet->get('moreColonizer')->getData();
                $fleetGive->setColonizer($fleetGive->getColonizer() + $form_manageFleet->get('moreColonizer')->getData());
            } elseif ($form_manageFleet->get('lessColonizer')->getData() <= $fleetGive->getColonizer()) {
                $colonizer = $fleetTake->getColonizer() + $form_manageFleet->get('lessColonizer')->getData();
                $fleetGive->setColonizer($fleetGive->getColonizer() - $form_manageFleet->get('lessColonizer')->getData());
            } else {
                $colonizer = $fleetTake->getColonizer();
            }
            if ($form_manageFleet->get('moreRecycleur')->getData()) {
                $recycleur = $fleetTake->getRecycleur() - $form_manageFleet->get('moreRecycleur')->getData();
                $fleetGive->setRecycleur($fleetGive->getRecycleur() + $form_manageFleet->get('moreRecycleur')->getData());
            } elseif ($form_manageFleet->get('lessRecycleur')->getData() <= $fleetGive->getRecycleur()) {
                $recycleur = $fleetTake->getRecycleur() + $form_manageFleet->get('lessRecycleur')->getData();
                $fleetGive->setRecycleur($fleetGive->getRecycleur() - $form_manageFleet->get('lessRecycleur')->getData());
            } else {
                $recycleur = $fleetTake->getRecycleur();
            }
            if ($form_manageFleet->get('moreCargoI')->getData()) {
                $cargoI = $fleetTake->getCargoI() - $form_manageFleet->get('moreCargoI')->getData();
                $fleetGive->setCargoI($fleetGive->getCargoI() + $form_manageFleet->get('moreCargoI')->getData());
            } elseif ($form_manageFleet->get('lessCargoI')->getData() <= $fleetGive->getCargoI()) {
                $cargoI = $fleetTake->getCargoI() + $form_manageFleet->get('lessCargoI')->getData();
                $fleetGive->setCargoI($fleetGive->getCargoI() - $form_manageFleet->get('lessCargoI')->getData());
            } else {
                $cargoI = $fleetTake->getCargoI();
            }
            if ($form_manageFleet->get('moreCargoV')->getData()) {
                $cargoV = $fleetTake->getCargoV() - $form_manageFleet->get('moreCargoV')->getData();
                $fleetGive->setCargoV($fleetGive->getCargoV() + $form_manageFleet->get('moreCargoV')->getData());
            } elseif ($form_manageFleet->get('lessCargoV')->getData() <= $fleetGive->getCargoV()) {
                $cargoV = $fleetTake->getCargoV() + $form_manageFleet->get('lessCargoV')->getData();
                $fleetGive->setCargoV($fleetGive->getCargoV() - $form_manageFleet->get('lessCargoV')->getData());
            } else {
                $cargoV = $fleetTake->getCargoV();
            }
            if ($form_manageFleet->get('moreCargoX')->getData()) {
                $cargoX = $fleetTake->getCargoX() - $form_manageFleet->get('moreCargoX')->getData();
                $fleetGive->setCargoX($fleetGive->getCargoX() + $form_manageFleet->get('moreCargoX')->getData());
            } elseif ($form_manageFleet->get('lessCargoX')->getData() <= $fleetGive->getCargoX()) {
                $cargoX = $fleetTake->getCargoX() + $form_manageFleet->get('lessCargoX')->getData();
                $fleetGive->setCargoX($fleetGive->getCargoX() - $form_manageFleet->get('lessCargoX')->getData());
            } else {
                $cargoX = $fleetTake->getCargoX();
            }
            if ($form_manageFleet->get('moreBarge')->getData()) {
                $barge = $fleetTake->getBarge() - $form_manageFleet->get('moreBarge')->getData();
                $fleetGive->setBarge($fleetGive->getBarge() + $form_manageFleet->get('moreBarge')->getData());
            } elseif ($form_manageFleet->get('lessBarge')->getData() <= $fleetGive->getBarge()) {
                $barge = $fleetTake->getBarge() + $form_manageFleet->get('lessBarge')->getData();
                $fleetGive->setBarge($fleetGive->getBarge() - $form_manageFleet->get('lessBarge')->getData());
            } else {
                $barge = $fleetTake->getBarge();
            }
            if ($form_manageFleet->get('moreSonde')->getData()) {
                $sonde = $fleetTake->getSonde() - $form_manageFleet->get('moreSonde')->getData();
                $fleetGive->setSonde($fleetGive->getSonde() + $form_manageFleet->get('moreSonde')->getData());
            } elseif ($form_manageFleet->get('lessSonde')->getData() <= $fleetGive->getSonde()) {
                $sonde = $fleetTake->getSonde() + $form_manageFleet->get('lessSonde')->getData();
                $fleetGive->setSonde($fleetGive->getSonde() - $form_manageFleet->get('lessSonde')->getData());
            } else {
                $sonde = $fleetTake->getSonde();
            }
            if ($form_manageFleet->get('moreHunter')->getData()) {
                $hunter = $fleetTake->getHunter() - $form_manageFleet->get('moreHunter')->getData();
                $fleetGive->setHunter($fleetGive->getHunter() + $form_manageFleet->get('moreHunter')->getData());
            } elseif ($form_manageFleet->get('lessHunter')->getData() <= $fleetGive->getHunter()) {
                $hunter = $fleetTake->getHunter() + $form_manageFleet->get('lessHunter')->getData();
                $fleetGive->setHunter($fleetGive->getHunter() - $form_manageFleet->get('lessHunter')->getData());
            } else {
                $hunter = $fleetTake->getHunter();
            }
            if ($form_manageFleet->get('moreHunterHeavy')->getData()) {
                $hunterHeavy = $fleetTake->getHunterHeavy() - $form_manageFleet->get('moreHunterHeavy')->getData();
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() + $form_manageFleet->get('moreHunterHeavy')->getData());
            } elseif ($form_manageFleet->get('lessHunterHeavy')->getData() <= $fleetGive->getHunterHeavy()) {
                $hunterHeavy = $fleetTake->getHunterHeavy() + $form_manageFleet->get('lessHunterHeavy')->getData();
                $fleetGive->setHunterHeavy($fleetGive->getHunterHeavy() - $form_manageFleet->get('lessHunterHeavy')->getData());
            } else {
                $hunterHeavy = $fleetTake->getHunterHeavy();
            }
            if ($form_manageFleet->get('moreCorvet')->getData()) {
                $corvet = $fleetTake->getCorvet() - $form_manageFleet->get('moreCorvet')->getData();
                $fleetGive->setCorvet($fleetGive->getCorvet() + $form_manageFleet->get('moreCorvet')->getData());
            } elseif ($form_manageFleet->get('lessCorvet')->getData() <= $fleetGive->getCorvet()) {
                $corvet = $fleetTake->getCorvet() + $form_manageFleet->get('lessCorvet')->getData();
                $fleetGive->setCorvet($fleetGive->getCorvet() - $form_manageFleet->get('lessCorvet')->getData());
            } else {
                $corvet = $fleetTake->getCorvet();
            }
            if ($form_manageFleet->get('moreCorvetLaser')->getData()) {
                $corvetLaser = $fleetTake->getCorvetLaser() - $form_manageFleet->get('moreCorvetLaser')->getData();
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() + $form_manageFleet->get('moreCorvetLaser')->getData());
            } elseif ($form_manageFleet->get('lessCorvetLaser')->getData() <= $fleetGive->getCorvetLaser()) {
                $corvetLaser = $fleetTake->getCorvetLaser() + $form_manageFleet->get('lessCorvetLaser')->getData();
                $fleetGive->setCorvetLaser($fleetGive->getCorvetLaser() - $form_manageFleet->get('lessCorvetLaser')->getData());
            } else {
                $corvetLaser = $fleetTake->getCorvetLaser();
            }
            if ($form_manageFleet->get('moreFregate')->getData()) {
                $fregate = $fleetTake->getFregate() - $form_manageFleet->get('moreFregate')->getData();
                $fleetGive->setFregate($fleetGive->getFregate() + $form_manageFleet->get('moreFregate')->getData());
            } elseif ($form_manageFleet->get('lessFregate')->getData() <= $fleetGive->getFregate()) {
                $fregate = $fleetTake->getFregate() + $form_manageFleet->get('lessFregate')->getData();
                $fleetGive->setFregate($fleetGive->getFregate() - $form_manageFleet->get('lessFregate')->getData());
            } else {
                $fregate = $fleetTake->getFregate();
            }
            if ($form_manageFleet->get('moreFregatePlasma')->getData()) {
                $fregatePlasma = $fleetTake->getFregatePlasma() - $form_manageFleet->get('moreFregatePlasma')->getData();
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() + $form_manageFleet->get('moreFregatePlasma')->getData());
            } elseif ($form_manageFleet->get('lessFregatePlasma')->getData() <= $fleetGive->getFregatePlasma()) {
                $fregatePlasma = $fleetTake->getFregatePlasma() + $form_manageFleet->get('lessFregatePlasma')->getData();
                $fleetGive->setFregatePlasma($fleetGive->getFregatePlasma() - $form_manageFleet->get('lessFregatePlasma')->getData());
            } else {
                $fregatePlasma = $fleetTake->getFregatePlasma();
            }
            if ($form_manageFleet->get('moreCroiser')->getData()) {
                $croiser = $fleetTake->getCroiser() - $form_manageFleet->get('moreCroiser')->getData();
                $fleetGive->setCroiser($fleetGive->getCroiser() + $form_manageFleet->get('moreCroiser')->getData());
            } elseif ($form_manageFleet->get('lessCroiser')->getData() <= $fleetGive->getCroiser()) {
                $croiser = $fleetTake->getCroiser() + $form_manageFleet->get('lessCroiser')->getData();
                $fleetGive->setCroiser($fleetGive->getCroiser() - $form_manageFleet->get('lessCroiser')->getData());
            } else {
                $croiser = $fleetTake->getCroiser();
            }
            if ($form_manageFleet->get('moreIronClad')->getData()) {
                $ironClad = $fleetTake->getIronClad() - $form_manageFleet->get('moreIronClad')->getData();
                $fleetGive->setIronClad($fleetGive->getIronClad() + $form_manageFleet->get('moreIronClad')->getData());
            } elseif ($form_manageFleet->get('lessIronClad')->getData() <= $fleetGive->getIronClad()) {
                $ironClad = $fleetTake->getIronClad() + $form_manageFleet->get('lessIronClad')->getData();
                $fleetGive->setIronClad($fleetGive->getIronClad() - $form_manageFleet->get('lessIronClad')->getData());
            } else {
                $ironClad = $fleetTake->getIronClad();
            }
            if ($form_manageFleet->get('moreDestroyer')->getData()) {
                $destroyer = $fleetTake->getDestroyer() - $form_manageFleet->get('moreDestroyer')->getData();
                $fleetGive->setDestroyer($fleetGive->getDestroyer() + $form_manageFleet->get('moreDestroyer')->getData());
            } elseif ($form_manageFleet->get('lessDestroyer')->getData() <= $fleetGive->getDestroyer()) {
                $destroyer = $fleetTake->getDestroyer() + $form_manageFleet->get('lessDestroyer')->getData();
                $fleetGive->setDestroyer($fleetGive->getDestroyer() - $form_manageFleet->get('lessDestroyer')->getData());
            } else {
                $destroyer = $fleetTake->getDestroyer();
            }
            $nbKeep = 0;
            if ($form_manageFleet->get('moreNiobium')->getData()) {
                $niobium = $fleetTake->getNiobium() - $form_manageFleet->get('moreNiobium')->getData();
                $fleetGive->setNiobium($fleetGive->getNiobium() + $form_manageFleet->get('moreNiobium')->getData());
            } elseif ($form_manageFleet->get('lessNiobium')->getData() <= $fleetGive->getNiobium()) {
                $niobium = $fleetTake->getNiobium() + $form_manageFleet->get('lessNiobium')->getData();
                $fleetGive->setNiobium($fleetGive->getNiobium() - $form_manageFleet->get('lessNiobium')->getData());
            } else {
                $niobium = 0;
                $nbKeep = 1;
            }
            $wtKeep = 0;
            if ($form_manageFleet->get('moreWater')->getData()) {
                $water = $fleetTake->getWater() - $form_manageFleet->get('moreWater')->getData();
                $fleetGive->setWater($fleetGive->getWater() + $form_manageFleet->get('moreWater')->getData());
            } elseif ($form_manageFleet->get('lessWater')->getData() <= $fleetGive->getWater()) {
                $water = $fleetTake->getWater() + $form_manageFleet->get('lessWater')->getData();
                $fleetGive->setWater($fleetGive->getWater() - $form_manageFleet->get('lessWater')->getData());
            } else {
                $water = 0;
                $wtKeep = 1;
            }
            $solKeep = 0;
            if ($form_manageFleet->get('moreSoldier')->getData()) {
                $soldier = $fleetTake->getSoldier() - $form_manageFleet->get('moreSoldier')->getData();
                $fleetGive->setSoldier($fleetGive->getSoldier() + $form_manageFleet->get('moreSoldier')->getData());
            } elseif ($form_manageFleet->get('lessSoldier')->getData() <= $fleetGive->getSoldier()) {
                $soldier = $fleetTake->getSoldier() + $form_manageFleet->get('lessSoldier')->getData();
                $fleetGive->setSoldier($fleetGive->getSoldier() - $form_manageFleet->get('lessSoldier')->getData());
            } else {
                $soldier = 0;
                $solKeep = 1;
            }
            $wkKeep = 0;
            if ($form_manageFleet->get('moreWorker')->getData()) {
                $worker = $fleetTake->getWorker() - $form_manageFleet->get('moreWorker')->getData();
                $fleetGive->setWorker($fleetGive->getWorker() + $form_manageFleet->get('moreWorker')->getData());
            } elseif ($form_manageFleet->get('lessWorker')->getData() <= $fleetGive->getWorker()) {
                $worker = $fleetTake->getWorker() + $form_manageFleet->get('lessWorker')->getData();
                $fleetGive->setWorker($fleetGive->getWorker() - $form_manageFleet->get('lessWorker')->getData());
            } else {
                $worker = 0;
                $wkKeep = 1;
            }
            $scKeep = 0;
            if ($form_manageFleet->get('moreScientist')->getData()) {
                $scientist = $fleetTake->getScientist() - $form_manageFleet->get('moreScientist')->getData();
                $fleetGive->setScientist($fleetGive->getScientist() + $form_manageFleet->get('moreScientist')->getData());
            } elseif ($form_manageFleet->get('lessScientist')->getData() <= $fleetGive->getScientist()) {
                $scientist = $fleetTake->getScientist() + $form_manageFleet->get('lessScientist')->getData();
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
                ($destroyer < 0 || $cargoX < 0 || $soldier > $fleetTake->getSoldierMax()) ||
                ($worker > $fleetTake->getWorkerMax() || $scientist > $fleetTake->getScientistMax())) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            if($fleetGive->getNbrShips() == 0) {
                $em->remove($fleetGive);
            } else {
                $em->persist($fleetGive);
            }
            $fleetTake->setColonizer($colonizer);
            $fleetTake->setCargoI($cargoI);
            $fleetTake->setCargoV($cargoV);
            $fleetTake->setCargoX($cargoX);
            $fleetTake->setRecycleur($recycleur);
            $fleetTake->setBarge($barge);
            $fleetTake->setSonde($sonde);
            $fleetTake->setHunter($hunter);
            $fleetTake->setHunterHeavy($hunterHeavy);
            $fleetTake->setCorvet($corvet);
            $fleetTake->setCorvetLaser($corvetLaser);
            $fleetTake->setFregate($fregate);
            $fleetTake->setFregatePlasma($fregatePlasma);
            $fleetTake->setCroiser($croiser);
            $fleetTake->setIronClad($ironClad);
            $fleetTake->setDestroyer($destroyer);
            if($nbKeep == 0) {
                $fleetTake->setNiobium($niobium);
            }
            if($wtKeep == 0) {
                $fleetTake->setWater($water);
            }
            if($solKeep == 0) {
                $fleetTake->setSoldier($soldier);
            }
            if($wkKeep == 0) {
                $fleetTake->setWorker($worker);
            }
            if($scKeep == 0) {
                $fleetTake->setScientist($scientist);
            }

            $em->persist($fleetTake);
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
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null) ||
            (($fleetTake->getSoldier() + $fleetGive->getSoldier()) <= $fleetTake->getSoldierMax() || ($fleetTake->getWorker() + $fleetGive->getWorker()) < $fleetTake->getWorkerMax()) ||
            ($fleetTake->getScientist() + $fleetGive->getScientist()) < $fleetTake->getScientistMax()) {
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
        $fleetTake->setCorvet($fleetTake->getCorvet() + $fleetGive->getCorvet());
        $fleetTake->setCorvetLaser($fleetTake->getCorvetLaser() + $fleetGive->getCorvetLaser());
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
        $em->persist($fleetTake);
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
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_sendFleet = $this->createForm(FleetSendType::class, null, array("user" => $user->getId()));
        $form_sendFleet->handleRequest($request);

        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_sendFleet->isSubmitted() && $form_sendFleet->isValid()) {
            if($form_sendFleet->get('planet')->getData()) {
                $fleetTake = $form_sendFleet->get('planet')->getData();
                $sector = $fleetTake->getSector()->getPosition();
                $fleetTakee = $fleetTake->getPosition();
                if($fleetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }
            } else {
                $galaxy = 1;
                $sector = $form_sendFleet->get('sector')->getData();
                $fleetTakee = $form_sendFleet->get('planete')->getData();

                if (($galaxy < 1 || $galaxy > 10) || ($sector < 1 || $sector > 100) || ($fleetTakee < 1 || $fleetTakee > 25) ||
                    ($galaxy != 1 && $user->getHyperespace() == 0)) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }

                $fleetTake = $em->getRepository('App:Planet')
                    ->createQueryBuilder('p')
                    ->join('p.sector', 's')
                    ->where('s.position = :sector')
                    ->andWhere('s.galaxy = :galaxy')
                    ->andWhere('p.position = :planete')
                    ->setParameters(array('sector' => $sector, 'galaxy' => $galaxy, 'planete' => $fleetTakee))
                    ->getQuery()
                    ->getOneOrNullResult();
                if($fleetTake == $fleetGive->getPlanet()) {
                    return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
                }
            }

            $sFleet= $fleetGive->getPlanet()->getSector()->getPosition();
            if ($sFleet == $sector) {
                $base= 2000;
            } elseif (strpos('0 -1 1 -10 10 -9 9', (strval($sFleet - $sector)) ) != false) {
                $base= 3000;
            } elseif (strpos('-20 20 12 11 8 2 -12 -11 -8 -2', (strval($sFleet - $sector)) ) != false) {
                $base= 6800;
            } elseif (strpos('-28 28 29 30 31 32 33 22 12 3 7 -29 -30 -31 -32 -33 -22 -13 -3 -7', (strval($sFleet - $sector)) ) != false) {
                $base= 8000;
            } else {
                $base= 12000;
            }
            $now->add(new DateInterval('PT' . ($fleetGive->getSpeed() * $base) . 'S'));
            $fleetGive->setRecycleAt(null);
            $fleetGive->setNewPlanet($fleetTake->getId());
            $fleetGive->setFlightTime($now);
            $fleetGive->setFlightType($form_sendFleet->get('flightType')->getData());
            $fleetGive->setSector($fleetTake->getSector());
            $fleetGive->setPlanete($fleetTakee);
            $em->persist($fleetGive);
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
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setNiobium($fleetTake->getNiobium() + $fleetGive->getNiobium());
        if($fleetTake->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getNiobium() / 1.5));
        }
        $fleetGive->setNiobium(0);
        $em->persist($fleetGive);
        $em->persist($fleetTake);
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
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setWater($fleetTake->getWater() + $fleetGive->getWater());
        if($fleetTake->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWater() * 2));
        }
        $fleetGive->setWater(0);
        $em->persist($fleetGive);
        $em->persist($fleetTake);
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
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setSoldier($fleetTake->getSoldier() + $fleetGive->getSoldier());
        if($fleetTake->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getSoldier() * 7.5));
        }
        $fleetGive->setSoldier(0);
        $em->persist($fleetGive);
        $em->persist($fleetTake);
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
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setWorker($fleetTake->getWorker() + $fleetGive->getWorker());
        if($fleetTake->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getWorker() / 4));
        }
        $fleetGive->setWorker(0);
        $em->persist($fleetGive);
        $em->persist($fleetTake);
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
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $fleetGive->getPlanet();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $fleetTake->setScientist($fleetTake->getScientist() + $fleetGive->getScientist());
        if($fleetTake->getMerchant() == true) {
            $user->setBitcoin($user->getBitcoin() + ($fleetGive->getScientist() * 75));
        }
        $fleetGive->setScientist(0);
        $em->persist($fleetGive);
        $em->persist($fleetTake);
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
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetTake = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->andWhere('f.user = :user')
            ->setParameters(array('id' => $id2, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();
        if(($fleetGive || $usePlanet) || ($fleetGive->getFightAt() == null || $fleetGive->getFlightTime() == null)) {
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
        $fleetTake->setCorvet($fleetTake->getCorvet() + $fleetGive->getCorvet());
        $fleetTake->setCorvetLaser($fleetTake->getCorvetLaser() + $fleetGive->getCorvetLaser());
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
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        if(($fleet || $usePlanet) || ($fleet->getFightAt() == null || $fleet->getFlightTime() == null) ||
            ($fleet->getPlanet()->getNbCdr() > 0 || $fleet->getPlanet()->getWtCdr() > 0) ||
            ($fleet->getRecycleur() || $fleet->getCargoPlace() > $fleet->getCargoFull())) {
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
}