<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialEditFleetType;
use App\Form\Front\FleetRenameType;
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

        $form_manageFleet = $this->createForm(SpatialEditFleetType::class);
        $form_manageFleet->handleRequest($request);

        $form_manageRenameFleet = $this->createForm(FleetRenameType::class, $fleet);
        $form_manageRenameFleet->handleRequest($request);

        $form_manageAttackFleet = $this->createForm(FleetAttackType::class, $fleet);
        $form_manageAttackFleet->handleRequest($request);

        $form_sendFleet = $this->createForm(FleetSendType::class);
        $form_sendFleet->handleRequest($request);

        if(($fleet || $usePlanet) || ($fleet->getFightAt() || $fleet->getFlightTime()) || $fleet->getPlanet()->getUser() == $user) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageRenameFleet->isSubmitted()) {
            $em->persist($fleet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageAttackFleet->isSubmitted()) {
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

            if(($fleet->getAttack() == true && $usePlanet->getFleetNoFriends()) || $fleets) {
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
            $em->persist($fleet);
            $em->flush();
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        if ($form_manageFleet->isSubmitted()) {

            if ($form_manageFleet->get('moreColonizer')->getData()) {
                $colonizer = $usePlanet->getColonizer() - $form_manageFleet->get('moreColonizer')->getData();
                $fleet->setColonizer($fleet->getColonizer() + $form_manageFleet->get('moreColonizer')->getData());
            } elseif ($form_manageFleet->get('lessColonizer')->getData() <= $fleet->getColonizer()) {
                $colonizer = $usePlanet->getColonizer() + $form_manageFleet->get('lessColonizer')->getData();
                $fleet->setColonizer($fleet->getColonizer() - $form_manageFleet->get('lessColonizer')->getData());
            } else {
                $colonizer = 0;
            }
            if ($form_manageFleet->get('moreRecycleur')->getData()) {
                $recycleur = $usePlanet->getRecycleur() - $form_manageFleet->get('moreRecycleur')->getData();
                $fleet->setRecycleur($fleet->getRecycleur() + $form_manageFleet->get('moreRecycleur')->getData());
            } elseif ($form_manageFleet->get('lessRecycleur')->getData() <= $fleet->getRecycleur()) {
                $recycleur = $usePlanet->getRecycleur() + $form_manageFleet->get('lessRecycleur')->getData();
                $fleet->setRecycleur($fleet->getRecycleur() - $form_manageFleet->get('lessRecycleur')->getData());
            } else {
                $recycleur = 0;
            }
            if ($form_manageFleet->get('moreBarge')->getData()) {
                $barge = $usePlanet->getBarge() - $form_manageFleet->get('moreBarge')->getData();
                $fleet->setBarge($fleet->getBarge() + $form_manageFleet->get('moreBarge')->getData());
            } elseif ($form_manageFleet->get('lessBarge')->getData() <= $fleet->getBarge()) {
                $barge = $usePlanet->getBarge() + $form_manageFleet->get('lessBarge')->getData();
                $fleet->setBarge($fleet->getBarge() - $form_manageFleet->get('lessBarge')->getData());
            } else {
                $barge = 0;
            }
            if ($form_manageFleet->get('moreSonde')->getData()) {
                $sonde = $usePlanet->getSonde() - $form_manageFleet->get('moreSonde')->getData();
                $fleet->setSonde($fleet->getSonde() + $form_manageFleet->get('moreSonde')->getData());
            } elseif ($form_manageFleet->get('lessSonde')->getData() <= $fleet->getSonde()) {
                $sonde = $usePlanet->getSonde() + $form_manageFleet->get('lessSonde')->getData();
                $fleet->setSonde($fleet->getSonde() - $form_manageFleet->get('lessSonde')->getData());
            } else {
                $sonde = 0;
            }
            if ($form_manageFleet->get('moreHunter')->getData()) {
                $hunter = $usePlanet->getHunter() - $form_manageFleet->get('moreHunter')->getData();
                $fleet->setHunter($fleet->getHunter() + $form_manageFleet->get('moreHunter')->getData());
            } elseif ($form_manageFleet->get('lessHunter')->getData() <= $fleet->getHunter()) {
                $hunter = $usePlanet->getHunter() + $form_manageFleet->get('lessHunter')->getData();
                $fleet->setHunter($fleet->getHunter() - $form_manageFleet->get('lessHunter')->getData());
            } else {
                $hunter = 0;
            }
            if ($form_manageFleet->get('moreFregate')->getData()) {
                $fregate = $usePlanet->getFregate() - $form_manageFleet->get('moreFregate')->getData();
                $fleet->setFregate($fleet->getFregate() + $form_manageFleet->get('moreFregate')->getData());
            } elseif ($form_manageFleet->get('lessFregate')->getData() <= $fleet->getFregate()) {
                $fregate = $usePlanet->getFregate() + $form_manageFleet->get('lessFregate')->getData();
                $fleet->setFregate($fleet->getFregate() - $form_manageFleet->get('lessFregate')->getData());
            } else {
                $fregate = 0;
            }
            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0)) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }

            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            } else {
                $em->persist($fleet);
            }
            $usePlanet->setColonizer($colonizer);
            $usePlanet->setRecycleur($recycleur);
            $usePlanet->setBarge($barge);
            $usePlanet->setSonde($sonde);
            $usePlanet->setHunter($hunter);
            $usePlanet->setFregate($fregate);
            $em->persist($usePlanet);
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

        if(($fleet || $usePlanet) || ($fleet->getFightAt() || $fleet->getFlightTime())) {
        } else {
            return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setColonizer($usePlanet->getColonizer() + $fleet->getColonizer());
        $usePlanet->setRecycleur($usePlanet->getRecycleur() + $fleet->getRecycleur());
        $usePlanet->setBarge($usePlanet->getBarge() + $fleet->getBarge());
        $usePlanet->setSonde($usePlanet->getSonde() + $fleet->getSonde());
        $usePlanet->setHunter($usePlanet->getHunter() + $fleet->getHunter());
        $usePlanet->setFregate($usePlanet->getFregate() + $fleet->getFregate());
        $em->remove($fleet);
        $em->persist($usePlanet);
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

        if(($fleet || $usePlanet) || ($fleet->getFightAt() || $fleet->getFlightTime())) {
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
                $base= 3600;
            } elseif (strpos('-20 20 12 11 8 2 -12 -11 -8 -2', (strval($sFleet - $sector)) ) != false) {
                $base= 7200;
            } elseif (strpos('-28 28 29 30 31 32 33 22 12 3 7 -29 -30 -31 -32 -33 -22 -13 -3 -7', (strval($sFleet - $sector)) ) != false) {
                $base= 10800;
            } else {
                $base= 18000;
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
}