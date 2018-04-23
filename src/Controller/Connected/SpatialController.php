<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialShipType;
use App\Form\Front\SpatialFleetType;
use App\Entity\Fleet;
use App\Entity\Ship;
use App\Entity\Yhip_Colonizer;
use App\Entity\Yhip_Sonde;
use App\Entity\Yhip_Hunter;
use App\Entity\Yhip_Fregate;
use App\Entity\Yhip_Barge;
use App\Entity\Yhip_Recycleur;

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_spatialShip = $this->createForm(SpatialShipType::class);
        $form_spatialShip->handleRequest($request);

        if($usePlanet->getBuilding()->getSpaceShip()) {
        } else {
            return $this->render('connected/spatial.html.twig', [
                'usePlanet' => $usePlanet,
                'form_spatialShip' => $form_spatialShip->createView(),
            ]);
        }
        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            $colonizer = $usePlanet->getShip()->getColonizer();
            $recycleur = $usePlanet->getShip()->getRecycleur();
            $barge = $usePlanet->getShip()->getBarge();
            $sonde = $usePlanet->getShip()->getSonde();
            $hunter = $usePlanet->getShip()->getHunter();
            $fregate = $usePlanet->getShip()->getFregate();
            $niobiumLess = ($colonizer->getNiobium() * $form_spatialShip->get('colonizer')->getData()) + ($recycleur->getNiobium() * $form_spatialShip->get('recycleur')->getData()) + ($barge->getNiobium() * $form_spatialShip->get('barge')->getData()) + ($sonde->getNiobium() * $form_spatialShip->get('sonde')->getData()) + ($hunter->getNiobium() * $form_spatialShip->get('hunter')->getData()) + ($fregate->getNiobium() * $form_spatialShip->get('fregate')->getData());
            $waterLess =  ($colonizer->getWater() * $form_spatialShip->get('colonizer')->getData()) + ($recycleur->getWater() * $form_spatialShip->get('recycleur')->getData()) + ($barge->getWater() * $form_spatialShip->get('barge')->getData()) + ($hunter->getWater() * $form_spatialShip->get('hunter')->getData()) + ($fregate->getWater() * $form_spatialShip->get('fregate')->getData());
            $bitcoinLess = ($colonizer->getBitcoin() * $form_spatialShip->get('colonizer')->getData()) + ($barge->getBitcoin() * $form_spatialShip->get('barge')->getData());

            if (($usePlanet->getNiobium() < $niobiumLess || $usePlanet->getWater() < $waterLess) || $user->getBitcoin() < $bitcoinLess) {
                return $this->render('connected/spatial.html.twig', [
                    'usePlanet' => $usePlanet,
                    'form_spatialShip' => $form_spatialShip->createView(),
                ]);
            }

            $colonizer->setAmount($colonizer->getAmount() + $form_spatialShip->get('colonizer')->getData());
            $recycleur->setAmount($recycleur->getAmount() + $form_spatialShip->get('recycleur')->getData());
            $barge->setAmount($barge->getAmount() + $form_spatialShip->get('barge')->getData());
            $sonde->setAmount($sonde->getAmount() + $form_spatialShip->get('sonde')->getData());
            $hunter->setAmount($hunter->getAmount() + $form_spatialShip->get('hunter')->getData());
            $fregate->setAmount($fregate->getAmount() + $form_spatialShip->get('fregate')->getData());
            $usePlanet->setNiobium($usePlanet->getNiobium() - $niobiumLess);
            $usePlanet->setWater($usePlanet->getWater() - $waterLess);
            $user->setBitcoin($user->getBitcoin() - $bitcoinLess);
            $em->persist($colonizer);
            $em->persist($barge);
            $em->persist($recycleur);
            $em->persist($sonde);
            $em->persist($hunter);
            $em->persist($fregate);
            $em->persist($usePlanet);
            $em->persist($user);
            $em->flush();
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

        if($usePlanet->getBuilding()->getSpaceShip()) {
        } else {
            return $this->render('connected/fleet/create.html.twig', [
                'usePlanet' => $usePlanet,
                'form_spatialShip' => $form_createFleet->createView(),
            ]);
        }
        if ($form_createFleet->isSubmitted() && $form_createFleet->isValid()) {
            $colonizer = $usePlanet->getShip()->getColonizer()->getAmount() - $form_createFleet->get('colonizer')->getData();
            $recycleur = $usePlanet->getShip()->getRecycleur()->getAmount() - $form_createFleet->get('recycleur')->getData();
            $barge = $usePlanet->getShip()->getBarge()->getAmount() - $form_createFleet->get('barge')->getData();
            $sonde = $usePlanet->getShip()->getSonde()->getAmount() - $form_createFleet->get('sonde')->getData();
            $hunter = $usePlanet->getShip()->getHunter()->getAmount() - $form_createFleet->get('hunter')->getData();
            $fregate = $usePlanet->getShip()->getFregate()->getAmount() - $form_createFleet->get('fregate')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0)) {
                return $this->render('connected/fleet/create.html.twig', [
                    'usePlanet' => $usePlanet,
                    'form_createFleet' => $form_createFleet->createView(),
                ]);
            }
            $fleet = new Fleet();
            $ship = new Ship();
            $colonizer = new Yhip_Colonizer();
            $barge = new Yhip_Barge();
            $recycleur = new Yhip_Recycleur();
            $sonde = new Yhip_Sonde();
            $hunter = new Yhip_Hunter();
            $fregate = new Yhip_Fregate();
            $colonizer->setAmount($form_createFleet->get('colonizer')->getData());
            $recycleur->setAmount($form_createFleet->get('recycleur')->getData());
            $barge->setAmount($form_createFleet->get('barge')->getData());
            $sonde->setAmount($form_createFleet->get('sonde')->getData());
            $hunter->setAmount($form_createFleet->get('hunter')->getData());
            $fregate->setAmount($form_createFleet->get('fregate')->getData());
            $ship->setColonizer($colonizer);
            $ship->setBarge($barge);
            $ship->setRecycleur($recycleur);
            $ship->setSonde($sonde);
            $ship->setHunter($hunter);
            $ship->setFregate($fregate);
            $em->persist($ship);
            $fleet->setShip($ship);
            $fleet->setUser($user);
            $fleet->setPlanet($usePlanet);
            $fleet->setName($form_createFleet->get('name')->getData());
            $em->persist($fleet);
            $usePlanet->getShip()->getColonizer()->setAmount($usePlanet->getShip()->getColonizer()->getAmount() - $colonizer->getAmount());
            $usePlanet->getShip()->getRecycleur()->setAmount($usePlanet->getShip()->getRecycleur()->getAmount() - $recycleur->getAmount());
            $usePlanet->getShip()->getBarge()->setAmount($usePlanet->getShip()->getBarge()->getAmount() - $barge->getAmount());
            $usePlanet->getShip()->getSonde()->setAmount($usePlanet->getShip()->getSonde()->getAmount() - $sonde->getAmount());
            $usePlanet->getShip()->getHunter()->setAmount($usePlanet->getShip()->getHunter()->getAmount() - $hunter->getAmount());
            $usePlanet->getShip()->getFregate()->setAmount($usePlanet->getShip()->getFregate()->getAmount() - $fregate->getAmount());
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