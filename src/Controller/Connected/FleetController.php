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
            $cargoRessources = $fleet->getCargoFull() + $form_manageFleet->get('moreNiobium')->getData() + $form_manageFleet->get('moreWater')->getData() + $form_manageFleet->get('moreSoldier')->getData() + $form_manageFleet->get('moreWorker')->getData() + $form_manageFleet->get('moreScientist')->getData();
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
            if ($form_manageFleet->get('moreCargoI')->getData()) {
                $cargoI = $planet->getCargoI() - $form_manageFleet->get('moreCargoI')->getData();
                $fleet->setCargoI($fleet->getCargoI() + $form_manageFleet->get('moreCargoI')->getData());
            } elseif ($form_manageFleet->get('lessCargoI')->getData() <= $fleet->getCargoI()) {
                $cargoI = $planet->getCargoI() + $form_manageFleet->get('lessCargoI')->getData();
                $fleet->setCargoI($fleet->getCargoI() - $form_manageFleet->get('lessCargoI')->getData());
            } else {
                $cargoI = $planet->getCargoI();
            }
            if ($form_manageFleet->get('moreCargoV')->getData()) {
                $cargoV = $planet->getCargoV() - $form_manageFleet->get('moreCargoV')->getData();
                $fleet->setCargoV($fleet->getCargoV() + $form_manageFleet->get('moreCargoV')->getData());
            } elseif ($form_manageFleet->get('lessCargoV')->getData() <= $fleet->getCargoV()) {
                $cargoV = $planet->getCargoV() + $form_manageFleet->get('lessCargoV')->getData();
                $fleet->setCargoV($fleet->getCargoV() - $form_manageFleet->get('lessCargoV')->getData());
            } else {
                $cargoV = $planet->getCargoV();
            }
            if ($form_manageFleet->get('moreCargoX')->getData()) {
                $cargoX = $planet->getCargoX() - $form_manageFleet->get('moreCargoX')->getData();
                $fleet->setCargoX($fleet->getCargoX() + $form_manageFleet->get('moreCargoX')->getData());
            } elseif ($form_manageFleet->get('lessCargoX')->getData() <= $fleet->getCargoX()) {
                $cargoX = $planet->getCargoX() + $form_manageFleet->get('lessCargoX')->getData();
                $fleet->setCargoX($fleet->getCargoX() - $form_manageFleet->get('lessCargoX')->getData());
            } else {
                $cargoX = $planet->getCargoX();
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
            if ($form_manageFleet->get('moreHunterHeavy')->getData()) {
                $hunterHeavy = $planet->getHunterHeavy() - $form_manageFleet->get('moreHunterHeavy')->getData();
                $fleet->setHunterHeavy($fleet->getHunterHeavy() + $form_manageFleet->get('moreHunterHeavy')->getData());
            } elseif ($form_manageFleet->get('lessHunterHeavy')->getData() <= $fleet->getHunterHeavy()) {
                $hunterHeavy = $planet->getHunterHeavy() + $form_manageFleet->get('lessHunterHeavy')->getData();
                $fleet->setHunterHeavy($fleet->getHunterHeavy() - $form_manageFleet->get('lessHunterHeavy')->getData());
            } else {
                $hunterHeavy = $planet->getHunterHeavy();
            }
            if ($form_manageFleet->get('moreCorvet')->getData()) {
                $corvet = $planet->getCorvet() - $form_manageFleet->get('moreCorvet')->getData();
                $fleet->setCorvet($fleet->getCorvet() + $form_manageFleet->get('moreCorvet')->getData());
            } elseif ($form_manageFleet->get('lessCorvet')->getData() <= $fleet->getCorvet()) {
                $corvet = $planet->getCorvet() + $form_manageFleet->get('lessCorvet')->getData();
                $fleet->setCorvet($fleet->getCorvet() - $form_manageFleet->get('lessCorvet')->getData());
            } else {
                $corvet = $planet->getCorvet();
            }
            if ($form_manageFleet->get('moreCorvetLaser')->getData()) {
                $corvetLaser = $planet->getCorvetLaser() - $form_manageFleet->get('moreCorvetLaser')->getData();
                $fleet->setCorvetLaser($fleet->getCorvetLaser() + $form_manageFleet->get('moreCorvetLaser')->getData());
            } elseif ($form_manageFleet->get('lessCorvetLaser')->getData() <= $fleet->getCorvetLaser()) {
                $corvetLaser = $planet->getCorvetLaser() + $form_manageFleet->get('lessCorvetLaser')->getData();
                $fleet->setCorvetLaser($fleet->getCorvetLaser() - $form_manageFleet->get('lessCorvetLaser')->getData());
            } else {
                $corvetLaser = $planet->getCorvetLaser();
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
            if ($form_manageFleet->get('moreFregatePlasma')->getData()) {
                $fregatePlasma = $planet->getFregatePlasma() - $form_manageFleet->get('moreFregatePlasma')->getData();
                $fleet->setFregatePlasma($fleet->getFregatePlasma() + $form_manageFleet->get('moreFregatePlasma')->getData());
            } elseif ($form_manageFleet->get('lessFregatePlasma')->getData() <= $fleet->getFregatePlasma()) {
                $fregatePlasma = $planet->getFregatePlasma() + $form_manageFleet->get('lessFregatePlasma')->getData();
                $fleet->setFregatePlasma($fleet->getFregatePlasma() - $form_manageFleet->get('lessFregatePlasma')->getData());
            } else {
                $fregatePlasma = $planet->getFregatePlasma();
            }
            if ($form_manageFleet->get('moreCroiser')->getData()) {
                $croiser = $planet->getCroiser() - $form_manageFleet->get('moreCroiser')->getData();
                $fleet->setCroiser($fleet->getCroiser() + $form_manageFleet->get('moreCroiser')->getData());
            } elseif ($form_manageFleet->get('lessCroiser')->getData() <= $fleet->getCroiser()) {
                $croiser = $planet->getCroiser() + $form_manageFleet->get('lessCroiser')->getData();
                $fleet->setCroiser($fleet->getCroiser() - $form_manageFleet->get('lessCroiser')->getData());
            } else {
                $croiser = $planet->getCroiser();
            }
            if ($form_manageFleet->get('moreIronClad')->getData()) {
                $ironClad = $planet->getIronClad() - $form_manageFleet->get('moreIronClad')->getData();
                $fleet->setIronClad($fleet->getIronClad() + $form_manageFleet->get('moreIronClad')->getData());
            } elseif ($form_manageFleet->get('lessIronClad')->getData() <= $fleet->getIronClad()) {
                $ironClad = $planet->getIronClad() + $form_manageFleet->get('lessIronClad')->getData();
                $fleet->setIronClad($fleet->getIronClad() - $form_manageFleet->get('lessIronClad')->getData());
            } else {
                $ironClad = $planet->getIronClad();
            }
            if ($form_manageFleet->get('moreDestroyer')->getData()) {
                $destroyer = $planet->getDestroyer() - $form_manageFleet->get('moreDestroyer')->getData();
                $fleet->setDestroyer($fleet->getDestroyer() + $form_manageFleet->get('moreDestroyer')->getData());
            } elseif ($form_manageFleet->get('lessDestroyer')->getData() <= $fleet->getDestroyer()) {
                $destroyer = $planet->getDestroyer() + $form_manageFleet->get('lessDestroyer')->getData();
                $fleet->setDestroyer($fleet->getDestroyer() - $form_manageFleet->get('lessDestroyer')->getData());
            } else {
                $destroyer = $planet->getDestroyer();
            }
            $nbKeep = 0;
            if ($form_manageFleet->get('moreNiobium')->getData()) {
                $niobium = $planet->getNiobium() - $form_manageFleet->get('moreNiobium')->getData();
                $fleet->setNiobium($fleet->getNiobium() + $form_manageFleet->get('moreNiobium')->getData());
            } elseif ($form_manageFleet->get('lessNiobium')->getData() <= $fleet->getNiobium()) {
                $niobium = $planet->getNiobium() + $form_manageFleet->get('lessNiobium')->getData();
                $fleet->setNiobium($fleet->getNiobium() - $form_manageFleet->get('lessNiobium')->getData());
            } else {
                $niobium = 0;
                $nbKeep = 1;
            }
            $wtKeep = 0;
            if ($form_manageFleet->get('moreWater')->getData()) {
                $water = $planet->getWater() - $form_manageFleet->get('moreWater')->getData();
                $fleet->setWater($fleet->getWater() + $form_manageFleet->get('moreWater')->getData());
            } elseif ($form_manageFleet->get('lessWater')->getData() <= $fleet->getWater()) {
                $water = $planet->getWater() + $form_manageFleet->get('lessWater')->getData();
                $fleet->setWater($fleet->getWater() - $form_manageFleet->get('lessWater')->getData());
            } else {
                $water = 0;
                $wtKeep = 1;
            }
            $solKeep = 0;
            if ($form_manageFleet->get('moreSoldier')->getData()) {
                $soldier = $planet->getSoldier() - $form_manageFleet->get('moreSoldier')->getData();
                $fleet->setSoldier($fleet->getSoldier() + $form_manageFleet->get('moreSoldier')->getData());
            } elseif ($form_manageFleet->get('lessSoldier')->getData() <= $fleet->getSoldier()) {
                $soldier = $planet->getSoldier() + $form_manageFleet->get('lessSoldier')->getData();
                $fleet->setSoldier($fleet->getSoldier() - $form_manageFleet->get('lessSoldier')->getData());
            } else {
                $soldier = 0;
                $solKeep = 1;
            }
            $wkKeep = 0;
            if ($form_manageFleet->get('moreWorker')->getData()) {
                $worker = $planet->getWorker() - $form_manageFleet->get('moreWorker')->getData();
                $fleet->setWorker($fleet->getWorker() + $form_manageFleet->get('moreWorker')->getData());
            } elseif ($form_manageFleet->get('lessWorker')->getData() <= $fleet->getWorker()) {
                $worker = $planet->getWorker() + $form_manageFleet->get('lessWorker')->getData();
                $fleet->setWorker($fleet->getWorker() - $form_manageFleet->get('lessWorker')->getData());
            } else {
                $worker = 0;
                $wkKeep = 1;
            }
            $scKeep = 0;
            if ($form_manageFleet->get('moreScientist')->getData()) {
                $scientist = $planet->getScientist() - $form_manageFleet->get('moreScientist')->getData();
                $fleet->setScientist($fleet->getScientist() + $form_manageFleet->get('moreScientist')->getData());
            } elseif ($form_manageFleet->get('lessScientist')->getData() <= $fleet->getScientist()) {
                $scientist = $planet->getScientist() + $form_manageFleet->get('lessScientist')->getData();
                $fleet->setScientist($fleet->getScientist() - $form_manageFleet->get('lessScientist')->getData());
            } else {
                $scientist = 0;
                $scKeep = 1;
            }
            $cargo = $fleet->getCargoPlace() - $cargoRessources;
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($niobium < 0 || $water < 0) || ($soldier < 0 || $worker < 0) || ($scientist < 0 || $cargo < 0) ||
                ($cargoI < 0 || $cargoV < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) ||
                ($destroyer < 0 || $cargoX < 0 || $soldier > $planet->getSoldierMax()) ||
                ($worker > $planet->getWorkerMax() || $scientist > $planet->getScientistMax())) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            } else {
                $em->persist($fleet);
            }
            $planet->setColonizer($colonizer);
            $planet->setCargoI($cargoI);
            $planet->setCargoV($cargoV);
            $planet->setCargoX($cargoX);
            $planet->setRecycleur($recycleur);
            $planet->setBarge($barge);
            $planet->setSonde($sonde);
            $planet->setHunter($hunter);
            $planet->setHunterHeavy($hunterHeavy);
            $planet->setCorvet($corvet);
            $planet->setCorvetLaser($corvetLaser);
            $planet->setFregate($fregate);
            $planet->setFregatePlasma($fregatePlasma);
            $planet->setCroiser($croiser);
            $planet->setIronClad($ironClad);
            $planet->setDestroyer($destroyer);
            if($nbKeep == 0) {
                $planet->setNiobium($niobium);
            }
            if($wtKeep == 0) {
                $planet->setWater($water);
            }
            if($solKeep == 0) {
                $planet->setSoldier($soldier);
            }
            if($wkKeep == 0) {
                $planet->setWorker($worker);
            }
            if($scKeep == 0) {
                $planet->setScientist($scientist);
            }

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
        $planet->setCargoI($planet->getCargoI() + $fleet->getCargoI());
        $planet->setCargoV($planet->getCargoV() + $fleet->getCargoV());
        $planet->setCargoX($planet->getCargoX() + $fleet->getCargoX());
        $planet->setRecycleur($planet->getRecycleur() + $fleet->getRecycleur());
        $planet->setBarge($planet->getBarge() + $fleet->getBarge());
        $planet->setSonde($planet->getSonde() + $fleet->getSonde());
        $planet->setHunter($planet->getHunter() + $fleet->getHunter());
        $planet->setHunterHeavy($planet->getHunterHeavy() + $fleet->getHunterHeavy());
        $planet->setCorvet($planet->getCorvet() + $fleet->getCorvet());
        $planet->setCorvetLaser($planet->getCorvetLaser() + $fleet->getCorvetLaser());
        $planet->setFregate($planet->getFregate() + $fleet->getFregate());
        $planet->setFregatePlasma($planet->getFregatePlasma() + $fleet->getFregatePlasma());
        $planet->setCroiser($planet->getCroiser() + $fleet->getCroiser());
        $planet->setIronClad($planet->getIronClad() + $fleet->getIronClad());
        $planet->setDestroyer($planet->getDestroyer() + $fleet->getDestroyer());
        $planet->setNiobium($planet->getNiobium() + $fleet->getNiobium());
        $planet->setWater($planet->getWater() + $fleet->getWater());
        if(($planet->getSoldier() + $fleet->getSoldier()) > $planet->getSoldierMax()) {
            $planet->setSoldier($planet->getSoldierMax());
        } else {
            $planet->setSoldier($planet->getSoldier() + $fleet->getSoldier());
        }
        if(($planet->getWorker() + $fleet->getWorker()) > $planet->getWorkerMax()) {
            $planet->setWorker($planet->getWorkerMax());
        } else {
            $planet->setWorker($planet->getWorker() + $fleet->getWorker());
        }
        if(($planet->getScientist() + $fleet->getScientist()) > $planet->getScientistMax()) {
            $planet->setScientist($planet->getScientistMax());
        } else {
            $planet->setScientist($planet->getScientist() + $fleet->getScientist());
        }
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
            if ($sFleet == $sector) {
                $base= 2000;
            } elseif (strpos('0 -1 1 -10 10 -9 9', (strval($sFleet - $sector)) ) != false) {
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