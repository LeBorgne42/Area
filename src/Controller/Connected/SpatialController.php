<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialShipType;
use App\Form\Front\SpatialFleetType;
use App\Entity\Fleet;
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
     * @Route("/chantier-spatiale/{idp}", name="spatial", requirements={"idp"="\d+"})
     */
    public function spatialAction(Request $request, $idp)
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
            $sonde = $form_spatialShip->get('sonde')->getData();
            $hunter = $form_spatialShip->get('hunter')->getData();
            $fregate = $form_spatialShip->get('fregate')->getData();
            $hunterHeavy = $form_spatialShip->get('hunterHeavy')->getData();
            $corvet = $form_spatialShip->get('corvet')->getData();
            $corvetLaser = $form_spatialShip->get('corvetLaser')->getData();
            $fregatePlasma = $form_spatialShip->get('fregatePlasma')->getData();
            $croiser = $form_spatialShip->get('croiser')->getData();
            $ironClad = $form_spatialShip->get('ironClad')->getData();
            $destroyer = $form_spatialShip->get('destroyer')->getData();
            $niobiumLess = (8000 * $cargoI) + (25000 * $cargoV) + (70000 * $cargoX) + (20000 * $colonizer) + (10000 * $recycleur) + (15000 * $barge) + (5 * $sonde) + (250 * $hunter) + (2200 * $fregate) + (400 * $hunterHeavy) + (1000 * $corvet) + (400 * $corvetLaser) + (2000 * $fregatePlasma) + (10000 * $croiser) + (30000 * $ironClad) + (20000 * $destroyer);
            $waterLess =  (6500 * $cargoI) + (18600 * $cargoV) + (57000 * $cargoX) + (12000 * $colonizer) + (7000 * $recycleur) + (12000 * $barge) + (50 * $hunter) + (1400 * $fregate) + (80 * $hunterHeavy) + (500 * $corvet) + (2000 * $corvetLaser) + (7000 * $fregatePlasma) + (8000 * $croiser) + (12000 * $ironClad) + (70000 * $destroyer);
            $workerLess = (5000 * $colonizer) + (500 * $destroyer) + (100 * $cargoX);

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
                ($destroyer && ($user->getMissile() != 3 || $usePlanet->getHeavyUsine() == 0 || $user->getHeavyShip() != 3 || $user->getPlasma() != 3 || $user->getLaser() != 3)))
            {
                return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
            }

            $usePlanet->setCargoI($usePlanet->getCargoI() + $cargoI);
            $usePlanet->setCargoV($usePlanet->getCargoV() + $cargoV);
            $usePlanet->setCargoX($usePlanet->getCargoX() + $cargoX);
            $usePlanet->setColonizer($usePlanet->getColonizer() + $colonizer);
            $usePlanet->setRecycleur($usePlanet->getRecycleur() + $recycleur);
            $usePlanet->setBarge($usePlanet->getBarge() + $barge);
            $usePlanet->setSonde($usePlanet->getSonde() + $sonde);
            $usePlanet->setHunter($usePlanet->getHunter() + $hunter);
            $usePlanet->setFregate($usePlanet->getFregate() + $fregate);
            $usePlanet->setHunterHeavy($usePlanet->getHunterHeavy() + $hunterHeavy);
            $usePlanet->setCorvet($usePlanet->getCorvet() + $corvet);
            $usePlanet->setCorvetLaser($usePlanet->getCorvetLaser() + $corvetLaser);
            $usePlanet->setFregatePlasma($usePlanet->getFregatePlasma() + $fregatePlasma);
            $usePlanet->setCroiser($usePlanet->getCroiser() + $croiser);
            $usePlanet->setIronClad($usePlanet->getIronClad() + $ironClad);
            $usePlanet->setDestroyer($usePlanet->getDestroyer() + $destroyer);
            $usePlanet->setNiobium($usePlanet->getNiobium() - $niobiumLess);
            $usePlanet->setWater($usePlanet->getWater() - $waterLess);
            $usePlanet->setWorker($usePlanet->getWorker() - $workerLess);
            $em->persist($usePlanet);
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
            $sonde = $usePlanet->getSonde() - $form_createFleet->get('sonde')->getData();
            $hunter = $usePlanet->getHunter() - $form_createFleet->get('hunter')->getData();
            $fregate = $usePlanet->getFregate() - $form_createFleet->get('fregate')->getData();
            $hunterHeavy = $usePlanet->getHunterHeavy() - $form_createFleet->get('hunterHeavy')->getData();
            $corvet = $usePlanet->getCorvet() - $form_createFleet->get('corvet')->getData();
            $corvetLaser = $usePlanet->getCorvetLaser() - $form_createFleet->get('corvetLaser')->getData();
            $fregatePlasma = $usePlanet->getFregatePlasma() - $form_createFleet->get('fregatePlasma')->getData();
            $croiser = $usePlanet->getCroiser() - $form_createFleet->get('croiser')->getData();
            $ironClad = $usePlanet->getIronClad() - $form_createFleet->get('ironClad')->getData();
            $destroyer = $usePlanet->getDestroyer() - $form_createFleet->get('destroyer')->getData();
            $total = $form_createFleet->get('cargoI')->getData() + $form_createFleet->get('cargoV')->getData() + $form_createFleet->get('cargoX')->getData() + $form_createFleet->get('hunterHeavy')->getData() + $form_createFleet->get('corvet')->getData() + $form_createFleet->get('corvetLaser')->getData() + $form_createFleet->get('fregatePlasma')->getData() + $form_createFleet->get('croiser')->getData() + $form_createFleet->get('ironClad')->getData() + $form_createFleet->get('destroyer')->getData() + $form_createFleet->get('colonizer')->getData() + $form_createFleet->get('fregate')->getData() + $form_createFleet->get('hunter')->getData() + $form_createFleet->get('sonde')->getData() + $form_createFleet->get('barge')->getData() + $form_createFleet->get('recycleur')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($total == 0 || $cargoI < 0) || ($cargoV < 0 || $cargoX < 0) || ($hunterHeavy < 0 || $corvet < 0) ||
                ($corvetLaser < 0 || $fregatePlasma < 0) || ($croiser < 0 || $ironClad < 0) || $destroyer < 0) {
                return $this->redirectToRoute('spatial', array('idp' => $usePlanet->getId()));
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
                ->setParameters(array('planet' => $usePlanet, 'true' => true, 'ally' => $warAlly, 'user' => $user))
                ->getQuery()
                ->getResult();

            $fleet = new Fleet();
            $fleet->setCargoI($form_createFleet->get('cargoI')->getData());
            $fleet->setCargoV($form_createFleet->get('cargoV')->getData());
            $fleet->setCargoX($form_createFleet->get('cargoX')->getData());
            $fleet->setColonizer($form_createFleet->get('colonizer')->getData());
            $fleet->setRecycleur($form_createFleet->get('recycleur')->getData());
            $fleet->setBarge($form_createFleet->get('barge')->getData());
            $fleet->setSonde($form_createFleet->get('sonde')->getData());
            $fleet->setHunter($form_createFleet->get('hunter')->getData());
            $fleet->setFregate($form_createFleet->get('fregate')->getData());
            $fleet->setHunterHeavy($form_createFleet->get('hunterHeavy')->getData());
            $fleet->setCorvet($form_createFleet->get('corvet')->getData());
            $fleet->setCorvetLaser($form_createFleet->get('corvetLaser')->getData());
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
            $usePlanet->setSonde($sonde);
            $usePlanet->setHunter($hunter);
            $usePlanet->setFregate($fregate);
            $usePlanet->setHunterHeavy($hunterHeavy);
            $usePlanet->setCorvet($corvet);
            $usePlanet->setCorvetLaser($corvetLaser);
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