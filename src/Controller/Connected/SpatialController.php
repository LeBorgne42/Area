<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialShipType;
use App\Form\Front\SpatialFleetType;
use App\Entity\Fleet;
use App\Entity\Product;
use Dateinterval;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SpatialController extends Controller
{
    /**
     * @Route("/chantier-spatial/{idp}", name="spatial", requirements={"idp"="\d+"})
     */
    public function spatialAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_spatialShip = $this->createForm(SpatialShipType::class);
        $form_spatialShip->handleRequest($request);

        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            if($usePlanet->getSpaceShip() == 0) {
                return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
            }
            $cargoI = $form_spatialShip->get('cargoI')->getData();
            $cargoV = $form_spatialShip->get('cargoV')->getData();
            $cargoX = $form_spatialShip->get('cargoX')->getData();
            $colonizer = $form_spatialShip->get('colonizer')->getData();
            $recycleur = $form_spatialShip->get('recycleur')->getData();
            $barge = $form_spatialShip->get('barge')->getData();
            $moonMaker = $form_spatialShip->get('moonMaker')->getData();
            $radarShip = $form_spatialShip->get('radarShip')->getData();
            $brouilleurShip = $form_spatialShip->get('brouilleurShip')->getData();
            $motherShip = $form_spatialShip->get('motherShip')->getData();
            $sonde = $form_spatialShip->get('sonde')->getData();
            $hunter = $form_spatialShip->get('hunter')->getData();
            $fregate = $form_spatialShip->get('fregate')->getData();
            $hunterHeavy = $form_spatialShip->get('hunterHeavy')->getData();
            $hunterWar = $form_spatialShip->get('hunterWar')->getData();
            $corvet = $form_spatialShip->get('corvet')->getData();
            $corvetLaser = $form_spatialShip->get('corvetLaser')->getData();
            $corvetWar = $form_spatialShip->get('corvetWar')->getData();
            $fregatePlasma = $form_spatialShip->get('fregatePlasma')->getData();
            $croiser = $form_spatialShip->get('croiser')->getData();
            $ironClad = $form_spatialShip->get('ironClad')->getData();
            $destroyer = $form_spatialShip->get('destroyer')->getData();
            $niobiumLess = (175000 * $motherShip) + (11000 * $brouilleurShip) + (5000 * $radarShip) + (500000 * $moonMaker) + (8000 * $cargoI) + (22000 * $cargoV) + (45000 * $cargoX) + (20000 * $colonizer) + (10000 * $recycleur) + (15000 * $barge) + (5 * $sonde) + (250 * $hunter) + (2200 * $fregate) + (400 * $hunterHeavy) + (1000 * $corvet) + (400 * $corvetLaser) + (2000 * $fregatePlasma) + (10000 * $croiser) + (30000 * $ironClad) + (20000 * $destroyer);
            $waterLess =  (95000 * $motherShip) + (13000 * $brouilleurShip) + (6000 * $radarShip) + (230000 * $moonMaker) + (6500 * $cargoI) + (15000 * $cargoV) + (38000 * $cargoX) + (12000 * $colonizer) + (7000 * $recycleur) + (12000 * $barge) + (50 * $hunter) + (1400 * $fregate) + (80 * $hunterHeavy) + (500 * $corvet) + (2000 * $corvetLaser) + (7000 * $fregatePlasma) + (8000 * $croiser) + (12000 * $ironClad) + (70000 * $destroyer);
            $workerLess = (2000 * $motherShip) + (20000 * $moonMaker) + (5000 * $colonizer) + (500 * $destroyer) + (50 * $cargoX);
            $warPoint = (500 * $motherShip) + (900 * $hunterWar) + (1800 * $corvetWar);
            $bitcoinLess = (200000 * $moonMaker) + (90000 * $brouilleurShip) +  (50000 * $radarShip);
            $time = (3600 * $motherShip) + (400 * $brouilleurShip) + (200 * $radarShip) + (18000 * $moonMaker) + ((300 * $cargoI) + (600 * $cargoV) + (900 * $cargoX) + (10800 * $colonizer) + (400 * $recycleur) + (1800 * $barge) + (2 * $sonde) + (60 * $hunterWar) + (300 * $corvetWar) + (20 * $hunter) + (240 * $fregate) + (32 * $hunterHeavy) + (100 * $corvet) + (160 * $corvetLaser) + (600 * $fregatePlasma) + (1200 * $croiser) + (2800 * $ironClad) + (6000 * $destroyer)) / $usePlanet->getShipProduction();
            $now->add(new DateInterval('PT' . round($time) . 'S'));

            if (($usePlanet->getNiobium() < $niobiumLess || $usePlanet->getWater() < $waterLess) ||
                ($usePlanet->getWorker() < $workerLess) || ($cargoI && $user->getCargo() < 1) ||
                ($cargoV && $user->getCargo() < 3) || ($cargoX && $user->getCargo() < 5) ||
                ($colonizer && $user->getTerraformation() == 0) || ($recycleur && $user->getRecycleur() == 0) ||
                ($barge && $user->getBarge() == 0) || ($hunter && ($user->getIndustry() == 0 || $user->getMissile() == 0)) ||
                ($fregate && ($user->getLaser() < 1 || $user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0)) ||
                ($hunterHeavy && ($user->getMissile() < 2 || $usePlanet->getLightUsine() == 0)) ||
                ($corvet && ($user->getMissile() != 3 || $usePlanet->getLightUsine() == 0 || $user->getLightShip() < 2)) ||
                ($corvetLaser && ($user->getMissile() != 3 || $usePlanet->getLightUsine() == 0 || $user->getLightShip() != 3 || $user->getLaser() < 1)) ||
                ($fregatePlasma && ($user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0 || $user->getPlasma() < 1 || $user->getLaser() < 1)) ||
                ($croiser && ($user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0 || $user->getPlasma() < 2 || $user->getLaser() < 2)) ||
                ($ironClad && ($user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0 || $user->getHeavyShip() < 2 || $user->getPlasma() != 3 || $user->getLaser() != 3)) ||
                ($destroyer && ($user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0 || $user->getHeavyShip() != 3 || $user->getPlasma() != 3 || $user->getLaser() != 3)) ||
                ($moonMaker && ($user->getTerraformation() < 15 || $usePlanet->getHeavyUsine() == 0)) ||
                ($radarShip && ($user->getOnde() < 3 || $usePlanet->getLightUsine() == 0)) ||
                ($brouilleurShip && ($user->getOnde() < 5 || $usePlanet->getLightUsine() == 0)) ||
                ($motherShip && ($user->getUtility() != 3 || $usePlanet->getHeavyUsine() == 0)) ||
                ($warPoint > $user->getRank()->getWarPoint() || $bitcoinLess > $user->getBitcoin()) ||
                ($user->getColonizer() && $colonizer) || ($motherShip && $user->getMotherShip()))
            {
                return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
            }
            if($usePlanet->getProduct()) {
                $reNow = new DateTime();
                $product = $usePlanet->getProduct();
                $product->setCargoI($product->getCargoI() + $cargoI);
                $product->setCargoV($product->getCargoV() + $cargoV);
                $product->setCargoX($product->getCargoX() + $cargoX);
                $product->setColonizer($product->getColonizer() + $colonizer);
                $product->setRecycleur($product->getRecycleur() + $recycleur);
                $product->setBarge($product->getBarge() + $barge);
                $product->setMoonMaker($product->getMoonMaker() + $moonMaker);
                $product->setRadarShip($product->getRadarShip() + $radarShip);
                $product->setBrouilleurShip($product->getBrouilleurShip() + $brouilleurShip);
                $product->setMotherShip($product->getMotherShip() + $motherShip);
                $product->setSonde($product->getSonde() + $sonde);
                $product->setHunter($product->getHunter() + $hunter);
                $product->setFregate($product->getFregate() + $fregate);
                $product->setHunterHeavy($product->getHunterHeavy() + $hunterHeavy);
                $product->setHunterWar($product->getHunterWar() + $hunterWar);
                $product->setCorvet($product->getCorvet() + $corvet);
                $product->setCorvetLaser($product->getCorvetLaser() + $corvetLaser);
                $product->setCorvetWar($product->getCorvetWar() + $corvetWar);
                $product->setFregatePlasma($product->getFregatePlasma() + $fregatePlasma);
                $product->setCroiser($product->getCroiser() + $croiser);
                $product->setIronClad($product->getIronClad() + $ironClad);
                $product->setDestroyer($product->getDestroyer() + $destroyer);
                $oldNow = $product->getProductAt();
                $tmpDate = $oldNow->diff($reNow);
                $reNow->add(new DateInterval('PT' . round($time) . 'S'));
                $reNow->sub($tmpDate);
                $product->setProductAt($reNow);
            } else {
                $product = new Product();
                $product->setPlanet($usePlanet);
                $product->setCargoI($cargoI);
                $product->setCargoV($cargoV);
                $product->setCargoX($cargoX);
                $product->setColonizer($colonizer);
                $product->setRecycleur($recycleur);
                $product->setBarge($barge);
                $product->setMoonMaker($moonMaker);
                $product->setRadarShip($radarShip);
                $product->setBrouilleurShip($brouilleurShip);
                $product->setMotherShip($motherShip);
                $product->setSonde($sonde);
                $product->setHunter($hunter);
                $product->setFregate($fregate);
                $product->setHunterHeavy($hunterHeavy);
                $product->setHunterWar($hunterWar);
                $product->setCorvet($corvet);
                $product->setCorvetLaser($corvetLaser);
                $product->setCorvetWar($corvetWar);
                $product->setFregatePlasma($fregatePlasma);
                $product->setCroiser($croiser);
                $product->setIronClad($ironClad);
                $product->setDestroyer($destroyer);
                $product->setProductAt($now);
            }

            $usePlanet->setNiobium($usePlanet->getNiobium() - $niobiumLess);
            $usePlanet->setWater($usePlanet->getWater() - $waterLess);
            $usePlanet->setWorker($usePlanet->getWorker() - $workerLess);
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() - $warPoint);
            $user->setBitcoin($user->getBitcoin() - $bitcoinLess);
            $em->persist($usePlanet);
            $em->persist($user);
            $em->persist($product);
            $em->flush();

            $form_spatialShip = null;
            $form_spatialShip = $this->createForm(SpatialShipType::class);
        }

        return $this->render('connected/spatial.html.twig', [
            'usePlanet' => $usePlanet,
            'form_spatialShip' => $form_spatialShip->createView(),
        ]);
    }

    /**
     * @Route("/creer-flotte/{idp}", name="create_fleet", requirements={"idp"="\d+"})
     */
    public function createFleetAction(Request $request, $idp)
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

        $form_createFleet = $this->createForm(SpatialFleetType::class);
        $form_createFleet->handleRequest($request);

        if ($form_createFleet->isSubmitted() && $form_createFleet->isValid()) {
            $cargoI = $usePlanet->getCargoI() - $form_createFleet->get('cargoI')->getData();
            $cargoV = $usePlanet->getCargoV() - $form_createFleet->get('cargoV')->getData();
            $cargoX = $usePlanet->getCargoX() - $form_createFleet->get('cargoX')->getData();
            $colonizer = $usePlanet->getColonizer() - $form_createFleet->get('colonizer')->getData();
            $recycleur = $usePlanet->getRecycleur() - $form_createFleet->get('recycleur')->getData();
            $barge = $usePlanet->getBarge() - $form_createFleet->get('barge')->getData();
            $moonMaker = $usePlanet->getMoonMaker() - $form_createFleet->get('moonMaker')->getData();
            $radarShip = $usePlanet->getRadarShip() - $form_createFleet->get('radarShip')->getData();
            $brouilleurShip = $usePlanet->getBrouilleurShip() - $form_createFleet->get('brouilleurShip')->getData();
            $motherShip = $usePlanet->getMotherShip() - $form_createFleet->get('motherShip')->getData();
            $sonde = $usePlanet->getSonde() - $form_createFleet->get('sonde')->getData();
            $hunter = $usePlanet->getHunter() - $form_createFleet->get('hunter')->getData();
            $fregate = $usePlanet->getFregate() - $form_createFleet->get('fregate')->getData();
            $hunterHeavy = $usePlanet->getHunterHeavy() - $form_createFleet->get('hunterHeavy')->getData();
            $hunterWar = $usePlanet->getHunterWar() - $form_createFleet->get('hunterWar')->getData();
            $corvet = $usePlanet->getCorvet() - $form_createFleet->get('corvet')->getData();
            $corvetLaser = $usePlanet->getCorvetLaser() - $form_createFleet->get('corvetLaser')->getData();
            $corvetWar = $usePlanet->getCorvetWar() - $form_createFleet->get('corvetWar')->getData();
            $fregatePlasma = $usePlanet->getFregatePlasma() - $form_createFleet->get('fregatePlasma')->getData();
            $croiser = $usePlanet->getCroiser() - $form_createFleet->get('croiser')->getData();
            $ironClad = $usePlanet->getIronClad() - $form_createFleet->get('ironClad')->getData();
            $destroyer = $usePlanet->getDestroyer() - $form_createFleet->get('destroyer')->getData();
            $total = $form_createFleet->get('motherShip')->getData() + $form_createFleet->get('brouilleurShip')->getData() + $form_createFleet->get('radarShip')->getData() + $form_createFleet->get('moonMaker')->getData() + $form_createFleet->get('corvetWar')->getData() + $form_createFleet->get('hunterWar')->getData() + $form_createFleet->get('cargoI')->getData() + $form_createFleet->get('cargoV')->getData() + $form_createFleet->get('cargoX')->getData() + $form_createFleet->get('hunterHeavy')->getData() + $form_createFleet->get('corvet')->getData() + $form_createFleet->get('corvetLaser')->getData() + $form_createFleet->get('fregatePlasma')->getData() + $form_createFleet->get('croiser')->getData() + $form_createFleet->get('ironClad')->getData() + $form_createFleet->get('destroyer')->getData() + $form_createFleet->get('colonizer')->getData() + $form_createFleet->get('fregate')->getData() + $form_createFleet->get('hunter')->getData() + $form_createFleet->get('sonde')->getData() + $form_createFleet->get('barge')->getData() + $form_createFleet->get('recycleur')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($total == 0 || $cargoI < 0) || ($cargoV < 0 || $cargoX < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) || ($destroyer < 0 || $hunterWar < 0) ||
                ($corvetWar < 0 || $moonMaker < 0 ) || ($radarShip < 0  || $brouilleurShip < 0 ) || ($motherShip < 0 )) {
                return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
            }
            $eAlly = $user->getAllyEnnemy();
            $warAlly = [];
            $x = 0;
            foreach ($eAlly as $tmp) {
                $warAlly[$x] = $tmp->getAllyTag();
                $x++;
            }

            $fAlly = $user->getAllyFriends();
            $friendAlly = [];
            $x = 0;
            foreach ($fAlly as $tmp) {
                $friendAlly[$x] = $tmp->getAllyTag();
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
                ->andWhere('a.sigle not in (:friend)')
                ->setParameters(array('planet' => $usePlanet, 'true' => true, 'ally' => $warAlly, 'user' => $user, 'friend' => $friendAlly))
                ->getQuery()
                ->getResult();

            $fleet = new Fleet();
            $fleet->setCargoI($form_createFleet->get('cargoI')->getData());
            $fleet->setCargoV($form_createFleet->get('cargoV')->getData());
            $fleet->setCargoX($form_createFleet->get('cargoX')->getData());
            $fleet->setColonizer($form_createFleet->get('colonizer')->getData());
            $fleet->setRecycleur($form_createFleet->get('recycleur')->getData());
            $fleet->setBarge($form_createFleet->get('barge')->getData());
            $fleet->setMoonMaker($form_createFleet->get('moonMaker')->getData());
            $fleet->setRadarShip($form_createFleet->get('radarShip')->getData());
            $fleet->setBrouilleurShip($form_createFleet->get('brouilleurShip')->getData());
            $fleet->setMotherShip($form_createFleet->get('motherShip')->getData());
            $fleet->setSonde($form_createFleet->get('sonde')->getData());
            $fleet->setHunter($form_createFleet->get('hunter')->getData());
            $fleet->setFregate($form_createFleet->get('fregate')->getData());
            $fleet->setHunterHeavy($form_createFleet->get('hunterHeavy')->getData());
            $fleet->setHunterWar($form_createFleet->get('hunterWar')->getData());
            $fleet->setCorvet($form_createFleet->get('corvet')->getData());
            $fleet->setCorvetLaser($form_createFleet->get('corvetLaser')->getData());
            $fleet->setCorvetWar($form_createFleet->get('corvetWar')->getData());
            $fleet->setFregatePlasma($form_createFleet->get('fregatePlasma')->getData());
            $fleet->setCroiser($form_createFleet->get('croiser')->getData());
            $fleet->setIronClad($form_createFleet->get('ironClad')->getData());
            $fleet->setDestroyer($form_createFleet->get('destroyer')->getData());
            if($fleets) {
                $allFleets = $em->getRepository('App:Fleet')
                    ->createQueryBuilder('f')
                    ->join('f.user', 'u')
                    ->where('f.planet = :planet')
                    ->andWhere('f.user != :user')
                    ->setParameters(array('planet' => $usePlanet, 'user' => $user))
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
            $fleet->setPlanet($usePlanet);
            $fleet->setName($form_createFleet->get('name')->getData());
            $em->persist($fleet);
            $usePlanet->setCargoI($cargoI);
            $usePlanet->setCargoV($cargoV);
            $usePlanet->setCargoX($cargoX);
            $usePlanet->setColonizer($colonizer);
            $usePlanet->setRecycleur($recycleur);
            $usePlanet->setBarge($barge);
            $usePlanet->setMoonMaker($moonMaker);
            $usePlanet->setRadarShip($radarShip);
            $usePlanet->setBrouilleurShip($brouilleurShip);
            $usePlanet->setMotherShip($motherShip);
            $usePlanet->setSonde($sonde);
            $usePlanet->setHunter($hunter);
            $usePlanet->setFregate($fregate);
            $usePlanet->setHunterHeavy($hunterHeavy);
            $usePlanet->setHunterWar($hunterWar);
            $usePlanet->setCorvet($corvet);
            $usePlanet->setCorvetLaser($corvetLaser);
            $usePlanet->setCorvetWar($corvetWar);
            $usePlanet->setFregatePlasma($fregatePlasma);
            $usePlanet->setCroiser($croiser);
            $usePlanet->setIronClad($ironClad);
            $usePlanet->setDestroyer($destroyer);
            $usePlanet->addFleet($fleet);
            $em->persist($usePlanet);
            $em->flush();


            return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/fleet/create.html.twig', [
            'usePlanet' => $usePlanet,
            'form_createFleet' => $form_createFleet->createView(),
        ]);
    }
}