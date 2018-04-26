<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialShipType;
use App\Form\Front\SpatialFleetType;
use App\Entity\Fleet;

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

        if($usePlanet->getSpaceShip()) {
        } else {
            return $this->render('connected/spatial.html.twig', [
                'usePlanet' => $usePlanet,
                'form_spatialShip' => $form_spatialShip->createView(),
            ]);
        }
        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            $colonizer = $form_spatialShip->get('colonizer')->getData();
            $recycleur = $form_spatialShip->get('recycleur')->getData();
            $barge = $form_spatialShip->get('barge')->getData();
            $sonde = $form_spatialShip->get('sonde')->getData();
            $hunter = $form_spatialShip->get('hunter')->getData();
            $fregate = $form_spatialShip->get('fregate')->getData();
            $niobiumLess = (20000 * $colonizer) + (10000 * $recycleur) + (15000 * $barge) + (5 * $sonde) + (25 * $hunter) + (300 * $fregate);
            $waterLess =  (12000 * $colonizer) + (7000 * $recycleur) + (12000 * $barge) + (5 * $hunter) + (200 * $fregate);

            if (($usePlanet->getNiobium() < $niobiumLess || $usePlanet->getWater() < $waterLess)) {
                return $this->render('connected/spatial.html.twig', [
                    'usePlanet' => $usePlanet,
                    'form_spatialShip' => $form_spatialShip->createView(),
                ]);
            }

            $usePlanet->setColonizer($usePlanet->getColonizer() + $colonizer);
            $usePlanet->setRecycleur($usePlanet->getRecycleur() + $recycleur);
            $usePlanet->setBarge($usePlanet->getBarge() + $barge);
            $usePlanet->setSonde($usePlanet->getSonde() + $sonde);
            $usePlanet->setHunter($usePlanet->getHunter() + $hunter);
            $usePlanet->setFregate($usePlanet->getFregate() + $fregate);
            $usePlanet->setNiobium($usePlanet->getNiobium() - $niobiumLess);
            $usePlanet->setWater($usePlanet->getWater() - $waterLess);
            $em->persist($usePlanet);
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

        if ($form_createFleet->isSubmitted() && $form_createFleet->isValid()) {
            $colonizer = $usePlanet->getColonizer() - $form_createFleet->get('colonizer')->getData();
            $recycleur = $usePlanet->getRecycleur() - $form_createFleet->get('recycleur')->getData();
            $barge = $usePlanet->getBarge() - $form_createFleet->get('barge')->getData();
            $sonde = $usePlanet->getSonde() - $form_createFleet->get('sonde')->getData();
            $hunter = $usePlanet->getHunter() - $form_createFleet->get('hunter')->getData();
            $fregate = $usePlanet->getFregate() - $form_createFleet->get('fregate')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0)) {
                return $this->render('connected/fleet/create.html.twig', [
                    'usePlanet' => $usePlanet,
                    'form_createFleet' => $form_createFleet->createView(),
                ]);
            }
            $fleet = new Fleet();
            $fleet->setColonizer($form_createFleet->get('colonizer')->getData());
            $fleet->setRecycleur($form_createFleet->get('recycleur')->getData());
            $fleet->setBarge($form_createFleet->get('barge')->getData());
            $fleet->setSonde($form_createFleet->get('sonde')->getData());
            $fleet->setHunter($form_createFleet->get('hunter')->getData());
            $fleet->setFregate($form_createFleet->get('fregate')->getData());
            $fleet->setUser($user);
            $fleet->setPlanet($usePlanet);
            $fleet->setName($form_createFleet->get('name')->getData());
            $em->persist($fleet);
            $usePlanet->setColonizer($colonizer);
            $usePlanet->setRecycleur($recycleur);
            $usePlanet->setBarge($barge);
            $usePlanet->setSonde($sonde);
            $usePlanet->setHunter($hunter);
            $usePlanet->setFregate($fregate);
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