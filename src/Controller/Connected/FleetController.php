<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialEditFleetType;

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/fleet.html.twig', [
            'usePlanet' => $usePlanet,
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

        $form_manageFleet = $this->createForm(SpatialEditFleetType::class, $fleet);
        $form_manageFleet->handleRequest($request);

        if($fleet || $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_manageFleet->isSubmitted() && $form_manageFleet->isValid()) {
            $ship = $fleet->getShip();

            if ($form_manageFleet->get('moreColonizer')->getData()) {
                $colonizer = $usePlanet->getShip()->getColonizer()->getAmount() - $form_manageFleet->get('moreColonizer')->getData();
                $ship->getColonizer()->setAmount($ship->getColonizer()->getAmount() + $form_manageFleet->get('moreColonizer')->getData());
            } elseif ($form_manageFleet->get('lessColonizer')->getData() <= $fleet->getShip()->getColonizer()->getAmount()) {
                $colonizer = $usePlanet->getShip()->getColonizer()->getAmount() + $form_manageFleet->get('lessColonizer')->getData();
                $ship->getColonizer()->setAmount($ship->getColonizer()->getAmount() - $form_manageFleet->get('lessColonizer')->getData());
            } else {
                $colonizer = 0;
            }
            if ($form_manageFleet->get('moreRecycleur')->getData()) {
                $recycleur = $usePlanet->getShip()->getRecycleur()->getAmount() - $form_manageFleet->get('moreRecycleur')->getData();
                $ship->getRecycleur()->setAmount($ship->getRecycleur()->getAmount() + $form_manageFleet->get('moreRecycleur')->getData());
            } elseif ($form_manageFleet->get('lessRecycleur')->getData() <= $fleet->getShip()->getRecycleur()->getAmount()) {
                $recycleur = $usePlanet->getShip()->getRecycleur()->getAmount() + $form_manageFleet->get('lessRecycleur')->getData();
                $ship->getRecycleur()->setAmount($ship->getRecycleur()->getAmount() - $form_manageFleet->get('lessRecycleur')->getData());
            } else {
                $recycleur = 0;
            }
            if ($form_manageFleet->get('moreBarge')->getData()) {
                $barge = $usePlanet->getShip()->getBarge()->getAmount() - $form_manageFleet->get('moreBarge')->getData();
                $ship->getBarge()->setAmount($ship->getBarge()->getAmount() + $form_manageFleet->get('moreBarge')->getData());
            } elseif ($form_manageFleet->get('lessBarge')->getData() <= $fleet->getShip()->getBarge()->getAmount()) {
                $barge = $usePlanet->getShip()->getBarge()->getAmount() + $form_manageFleet->get('lessBarge')->getData();
                $ship->getBarge()->setAmount($ship->getBarge()->getAmount() - $form_manageFleet->get('lessBarge')->getData());
            } else {
                $barge = 0;
            }
            if ($form_manageFleet->get('moreSonde')->getData()) {
                $sonde = $usePlanet->getShip()->getSonde()->getAmount() - $form_manageFleet->get('moreSonde')->getData();
                $ship->getSonde()->setAmount($ship->getSonde()->getAmount() + $form_manageFleet->get('moreSonde')->getData());
            } elseif ($form_manageFleet->get('lessSonde')->getData() <= $fleet->getShip()->getSonde()->getAmount()) {
                $sonde = $usePlanet->getShip()->getSonde()->getAmount() + $form_manageFleet->get('lessSonde')->getData();
                $ship->getSonde()->setAmount($ship->getSonde()->getAmount() - $form_manageFleet->get('lessSonde')->getData());
            } else {
                $sonde = 0;
            }
            if ($form_manageFleet->get('moreHunter')->getData()) {
                $hunter = $usePlanet->getShip()->getHunter()->getAmount() - $form_manageFleet->get('moreHunter')->getData();
                $ship->getHunter()->setAmount($ship->getHunter()->getAmount() + $form_manageFleet->get('moreHunter')->getData());
            } elseif ($form_manageFleet->get('lessHunter')->getData() <= $fleet->getShip()->getHunter()->getAmount()) {
                $hunter = $usePlanet->getShip()->getHunter()->getAmount() + $form_manageFleet->get('lessHunter')->getData();
                $ship->getHunter()->setAmount($ship->getHunter()->getAmount() - $form_manageFleet->get('lessHunter')->getData());
            } else {
                $hunter = 0;
            }
            if ($form_manageFleet->get('moreFregate')->getData()) {
                $fregate = $usePlanet->getShip()->getFregate()->getAmount() - $form_manageFleet->get('moreFregate')->getData();
                $ship->getFregate()->setAmount($ship->getFregate()->getAmount() + $form_manageFleet->get('moreFregate')->getData());
            } elseif ($form_manageFleet->get('lessFregate')->getData() <= $fleet->getShip()->getFregate()->getAmount()) {
                $fregate = $usePlanet->getShip()->getFregate()->getAmount() + $form_manageFleet->get('lessFregate')->getData();
                $ship->getFregate()->setAmount($ship->getFregate()->getAmount() - $form_manageFleet->get('lessFregate')->getData());
            } else {
                $fregate = 0;
            }
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0)) {
                exit;
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            $em->persist($ship);
            $fleet->setShip($ship);
            $fleet->setName($form_manageFleet->get('name')->getData());
            $em->persist($fleet);
            $usePlanet->getShip()->getColonizer()->setAmount($colonizer);
            $usePlanet->getShip()->getRecycleur()->setAmount($recycleur);
            $usePlanet->getShip()->getBarge()->setAmount($barge);
            $usePlanet->getShip()->getSonde()->setAmount($sonde);
            $usePlanet->getShip()->getHunter()->setAmount($hunter);
            $usePlanet->getShip()->getFregate()->setAmount($fregate);
            $em->persist($usePlanet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/fleet/edit.html.twig', [
            'fleet' => $fleet,
            'usePlanet' => $usePlanet,
            'form_manageFleet' => $form_manageFleet->createView(),
        ]);
    }

    /**
     * @Route("/detruire-flotte/{idp}/{id}", name="destroy_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function destroyFleetAction(Request $request, $idp, $id)
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

        $form_manageFleet = $this->createForm(SpatialEditFleetType::class, $fleet);
        $form_manageFleet->handleRequest($request);

        if($fleet || $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_manageFleet->isSubmitted() && $form_manageFleet->isValid()) {
            $colonizer = $usePlanet->getShip()->getColonizer()->getAmount() - $form_manageFleet->get('moreColonizer')->getData();
            $recycleur = $usePlanet->getShip()->getRecycleur()->getAmount() - $form_manageFleet->get('moreRecycleur')->getData();
            $barge = $usePlanet->getShip()->getBarge()->getAmount() - $form_manageFleet->get('moreBarge')->getData();
            $sonde = $usePlanet->getShip()->getSonde()->getAmount() - $form_manageFleet->get('moreSonde')->getData();
            $hunter = $usePlanet->getShip()->getHunter()->getAmount() - $form_manageFleet->get('moreHunter')->getData();
            $fregate = $usePlanet->getShip()->getFregate()->getAmount() - $form_manageFleet->get('moreFregate')->getData();
            $colonizerLess = $usePlanet->getShip()->getColonizer()->getAmount() + $form_manageFleet->get('lessColonizer')->getData();
            $recycleurLess = $usePlanet->getShip()->getRecycleur()->getAmount() + $form_manageFleet->get('lessRecycleur')->getData();
            $bargeLess = $usePlanet->getShip()->getBarge()->getAmount() + $form_manageFleet->get('lessBarge')->getData();
            $sondeLess = $usePlanet->getShip()->getSonde()->getAmount() + $form_manageFleet->get('lessSonde')->getData();
            $hunterLess = $usePlanet->getShip()->getHunter()->getAmount() + $form_manageFleet->get('lessHunter')->getData();
            $fregateLess = $usePlanet->getShip()->getFregate()->getAmount() + $form_manageFleet->get('lessFregate')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($form_manageFleet->get('lessColonizer')->getData() > $fleet->getColonizer->getAmount() ||
                    $form_manageFleet->get('lessRecycleur')->getData() > $fleet->getRecycleur->getAmount()) ||
                ($form_manageFleet->get('lessBarge')->getData() > $fleet->getBarge->getAmount() ||
                    $form_manageFleet->get('lessSonde')->getData() > $fleet->getSonde->getAmount()) ||
                ($form_manageFleet->get('lessHunter')->getData() > $fleet->getHunter->getAmount() ||
                    $form_manageFleet->get('lessFregate')->getData() > $fleet->getFregate->getAmount())) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
            $ship = $fleet->getShip();
            $colonizer->setAmount($form_manageFleet->get('colonizer')->getData());
            $recycleur->setAmount($form_manageFleet->get('recycleur')->getData());
            $barge->setAmount($form_manageFleet->get('barge')->getData());
            $sonde->setAmount($form_manageFleet->get('sonde')->getData());
            $hunter->setAmount($form_manageFleet->get('hunter')->getData());
            $fregate->setAmount($form_manageFleet->get('fregate')->getData());
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
            $fleet->setName($form_manageFleet->get('name')->getData());
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

        return $this->render('connected/fleet/edit.html.twig', [
            'id' => $id,
            'usePlanet' => $usePlanet,
            'form_manageFleet' => $form_manageFleet->createView(),
        ]);
    }

    /**
     * @Route("/envoyer-flotte/{idp}/{id}", name="send_fleet", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function sendFleetAction(Request $request, $idp, $id)
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

        $form_manageFleet = $this->createForm(SpatialEditFleetType::class, $fleet);
        $form_manageFleet->handleRequest($request);

        if($fleet || $usePlanet) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }
        if ($form_manageFleet->isSubmitted() && $form_manageFleet->isValid()) {
            $colonizer = $usePlanet->getShip()->getColonizer()->getAmount() - $form_manageFleet->get('moreColonizer')->getData();
            $recycleur = $usePlanet->getShip()->getRecycleur()->getAmount() - $form_manageFleet->get('moreRecycleur')->getData();
            $barge = $usePlanet->getShip()->getBarge()->getAmount() - $form_manageFleet->get('moreBarge')->getData();
            $sonde = $usePlanet->getShip()->getSonde()->getAmount() - $form_manageFleet->get('moreSonde')->getData();
            $hunter = $usePlanet->getShip()->getHunter()->getAmount() - $form_manageFleet->get('moreHunter')->getData();
            $fregate = $usePlanet->getShip()->getFregate()->getAmount() - $form_manageFleet->get('moreFregate')->getData();
            $colonizerLess = $usePlanet->getShip()->getColonizer()->getAmount() + $form_manageFleet->get('lessColonizer')->getData();
            $recycleurLess = $usePlanet->getShip()->getRecycleur()->getAmount() + $form_manageFleet->get('lessRecycleur')->getData();
            $bargeLess = $usePlanet->getShip()->getBarge()->getAmount() + $form_manageFleet->get('lessBarge')->getData();
            $sondeLess = $usePlanet->getShip()->getSonde()->getAmount() + $form_manageFleet->get('lessSonde')->getData();
            $hunterLess = $usePlanet->getShip()->getHunter()->getAmount() + $form_manageFleet->get('lessHunter')->getData();
            $fregateLess = $usePlanet->getShip()->getFregate()->getAmount() + $form_manageFleet->get('lessFregate')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($form_manageFleet->get('lessColonizer')->getData() > $fleet->getColonizer->getAmount() ||
                    $form_manageFleet->get('lessRecycleur')->getData() > $fleet->getRecycleur->getAmount()) ||
                ($form_manageFleet->get('lessBarge')->getData() > $fleet->getBarge->getAmount() ||
                    $form_manageFleet->get('lessSonde')->getData() > $fleet->getSonde->getAmount()) ||
                ($form_manageFleet->get('lessHunter')->getData() > $fleet->getHunter->getAmount() ||
                    $form_manageFleet->get('lessFregate')->getData() > $fleet->getFregate->getAmount())) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
            $ship = $fleet->getShip();
            $colonizer->setAmount($form_manageFleet->get('colonizer')->getData());
            $recycleur->setAmount($form_manageFleet->get('recycleur')->getData());
            $barge->setAmount($form_manageFleet->get('barge')->getData());
            $sonde->setAmount($form_manageFleet->get('sonde')->getData());
            $hunter->setAmount($form_manageFleet->get('hunter')->getData());
            $fregate->setAmount($form_manageFleet->get('fregate')->getData());
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
            $fleet->setName($form_manageFleet->get('name')->getData());
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

        return $this->render('connected/fleet/edit.html.twig', [
            'id' => $id,
            'usePlanet' => $usePlanet,
            'form_manageFleet' => $form_manageFleet->createView(),
        ]);
    }
}