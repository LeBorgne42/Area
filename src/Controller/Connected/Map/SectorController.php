<?php

namespace App\Controller\Connected\Map;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\NavigateType;
use App\Form\Front\InteractFleetType;
use App\Entity\Fleet;
use Datetime;
use DatetimeZone;
use DateInterval;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class SectorController extends AbstractController
{
    /**
     * @Route("/carte-spatiale/{id}/{gal}/{idp}", name="map", requirements={"id"="\d+", "idp"="\d+", "gal"="\d+"})
     */
    public function mapAction(Request $request, $id, $idp, $gal)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_navigate = $this->createForm(NavigateType::class, null, ["galaxy" => $gal, "sector" => $id]);
        $form_navigate->handleRequest($request);

        if ($form_navigate->isSubmitted() && $form_navigate->isValid()) {
            if ($form_navigate->get('sector')->getData() && $form_navigate->get('galaxy')->getData()) {
                return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $form_navigate->get('sector')->getData(), 'gal' => $form_navigate->get('galaxy')->getData()]);
            }
            return $this->redirectToRoute('galaxy', ['idp' => $usePlanet->getId(), 'id' => $form_navigate->get('galaxy')->getData()]);
        }

        $sectorId = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('s.position = :id')
            ->andWhere('g.position = :gal')
            ->setParameters(['id' => $id, 'gal' => $gal])
            ->getQuery()
            ->getOneOrNullResult();

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->join('p.sector', 's')
            ->join('s.galaxy', 'g')
            ->where('s.position = :id')
            ->andWhere('g.position = :gal')
            ->setParameters(['id' => $id, 'gal' => $gal])
            ->orderBy('p.position')
            ->getQuery()
            ->getResult();

        $fleetIn = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->leftJoin('f.planet', 'p')
            ->join('p.sector', 'ps')
            ->join('f.sector', 'fs')
            ->join('fs.galaxy', 'g')
            ->where('fs.position = :sec')
            ->andWhere('g.position = :gal')
            ->andWhere('ps.position != :id')
            ->setParameters(['id' => $id, 'gal' => $gal, 'sec' => $sectorId->getPosition()])
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        $fleetOut = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->join('p.sector', 'ps')
            ->join('ps.galaxy', 'g')
            ->join('f.sector', 'fs')
            ->where('fs.position != :sec')
            ->andWhere('ps.position = :id')
            ->andWhere('g.position = :gal')
            ->setParameters(['id' => $id, 'gal' => $gal, 'sec' => $sectorId->getPosition()])
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        $fleetCurrent = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->join('p.sector', 'ps')
            ->join('ps.galaxy', 'gp')
            ->join('f.sector', 'fs')
            ->join('fs.galaxy', 'gs')
            ->where('ps.position = :id')
            ->andWhere('fs.position = :id')
            ->andWhere('gs.position = :gal')
            ->andWhere('gp.position = :gal')
            ->setParameters(['id' => $id, 'gal' => $gal])
            ->orderBy('f.flightTime')
            ->getQuery()
            ->getResult();

        foreach ($planets as $planet) {
            $id = $planet->getSector()->getPosition();
            $gal = $planet->getSector()->getGalaxy()->getPosition();
            break;
        }

        return $this->render('connected/map/sector.html.twig', [
            'form_navigate' => $form_navigate->createView(),
            'planets' => $planets,
            'usePlanet' => $usePlanet,
            'id' => $id,
            'gal' => $gal,
            'fleetIn' => $fleetIn,
            'fleetOut' => $fleetOut,
            'fleetCurrent' => $fleetCurrent,
        ]);
    }

    /**
     * @Route("/flotte-orbite/{id}/{idp}", name="fleet_sector", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function fleetAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.planet', 'p')
            ->where('p.id = :id')
            ->andWhere('f.flightTime is null')
            ->setParameters(['id' => $id])
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/fleet.html.twig', [
            'fleets' => $fleets,
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/interaction-planete/{id}/{idp}", name="planet_interact", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function planetInteractAction(Request $request, $id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $planet = $em->getRepository('App:Planet')->find(['id' => $id]);

        $form_sendFleet = $this->createForm(InteractFleetType::class, null, ["user" => $user->getId()]);
        $form_sendFleet->handleRequest($request);

        if($planet && $usePlanet) {
        } else {
            return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
        }
        if ($form_sendFleet->isSubmitted() && $form_sendFleet->isValid()) {
            $fleet = $form_sendFleet->get('list')->getData();
            $sFleet= $fleet->getPlanet()->getSector()->getPosition();
            $sector = $planet->getSector()->getPosition();
            $planete = $planet->getPosition();
            $galaxy = $planet->getSector()->getGalaxy()->getPosition();
            if($user->getHyperespace() != 1 && $fleet->getPlanet()->getSector()->getGalaxy()->getPosition() != $galaxy) {
                return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
            }
            if($fleet->getPlanet()->getSector()->getGalaxy()->getPosition() != $galaxy) {
                $base = 100000;
                $price = 25;
            } else {
                $pFleet = $fleet->getPlanet()->getPosition();
                $x1 = ($sFleet % 10) * 5 + ($pFleet % 5);
                $x2 = ($sector % 10) * 5 + ($planete % 5);
                $y1 = ($sFleet / 10) * 5 + ($pFleet % 5);
                $y2 = ($sector / 10) * 5 + ($planete % 5);
                $base = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
                $price = $base / 3;
            }
            $carburant = round($price * ($fleet->getNbrSignatures() / 200));
            if($carburant > $user->getBitcoin()) {
                return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
            }
            if($fleet->getMotherShip()) {
                $speed = $fleet->getSpeed() - ($fleet->getSpeed() * 0.10);
            } else {
                $speed = $fleet->getSpeed();
            }
            $distance = $speed * $base * 500;
            $now->add(new DateInterval('PT' . round($distance) . 'S'));
            $moreNow = new DateTime();
            $moreNow->setTimezone(new DateTimeZone('Europe/Paris'));
            $moreNow->add(new DateInterval('PT' . 120 . 'S'));
            $fleet->setRecycleAt(null);
            $fleet->setNewPlanet($planet->getId());
            $fleet->setFlightTime($now);
            $fleet->setSector($planet->getSector());
            $fleet->setPlanete($planete);
            $fleet->setFlightType($form_sendFleet->get('flightType')->getData());
            $fleet->setCancelFlight($moreNow);
            $user->setBitcoin($user->getBitcoin() - $carburant);

            $em->flush();
            return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
        }

        return $this->render('connected/map/interact.html.twig', [
            'usePlanet' => $usePlanet,
            'planet' => $planet,
            'form_fleetMove' => $form_sendFleet->createView(),
        ]);
    }

    /**
     * @Route("/envoyer-sonde/{id}/{idp}", name="send_sonde", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function sendSondeAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $planet = $em->getRepository('App:Planet')->find(['id' => $id]);

        $fPlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->andWhere('p.user = :user')
            ->andwhere('p.sonde > :zero')
            ->setParameters(['user' => $user, 'zero' => 0])
            ->getQuery()
            ->setMaxResults(1)
            ->getOneOrNullResult();

        if($fPlanet == null) {
            return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
        }
        $sFleet= $fPlanet->getSector()->getPosition();
        $sector = $planet->getSector()->getPosition();
        $planete = $planet->getPosition();
        $galaxy = $planet->getSector()->getGalaxy()->getPosition();
        if($user->getHyperespace() == 0 && $fPlanet->getSector()->getGalaxy()->getPosition() != $galaxy) {
            return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $fPlanet->getSector()->getPosition(), 'gal' => $fPlanet->getSector()->getGalaxy()->getPosition()]);
        }
        if($fPlanet->getSector()->getGalaxy()->getPosition() != $galaxy) {
            $base = 100000;
            $price = 25;
        } else {
            $pFleet = $fPlanet->getPosition();
            $x1 = ($sFleet % 10) * 5 + ($pFleet % 5);
            $x2 = ($sector % 10) * 5 + ($planete % 5);
            $y1 = ($sFleet / 10) * 5 + ($pFleet % 5);
            $y2 = ($sector / 10) * 5 + ($planete % 5);
            $base = sqrt(pow(($x2 - $x1), 2) + pow(($y2 - $y1), 2));
            $price = $base / 3;
        }
        $carburant = round($price * ($fPlanet->getNbrSignatures() / 200));
        if(1 > $user->getBitcoin()) {
            return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
        }
        $fleet = new Fleet();
        $fleet->setSonde(1);
        $fleet->setUser($user);
        $fleet->setPlanet($fPlanet);
        $fleet->setName('Auto Sonde');
        $fPlanet->setSonde($fPlanet->getSonde() - 1);
        $speed = $fleet->getSpeed();
        $distance = $speed * $base * 500;
        $now->add(new DateInterval('PT' . round($distance) . 'S'));
        $moreNow = new DateTime();
        $moreNow->setTimezone(new DateTimeZone('Europe/Paris'));
        $moreNow->add(new DateInterval('PT' . 120 . 'S'));
        $fleet->setNewPlanet($planet->getId());
        $fleet->setFlightTime($now);
        $fleet->setSector($planet->getSector());
        $fleet->setPlanete($planete);
        $fleet->setFlightType(1);
        $fleet->setCancelFlight($moreNow);
        $user->setBitcoin($user->getBitcoin() - $carburant);
        $em->persist($fleet);
        if ($planet->getUser()) {
            $quest = $user->checkQuests('spy_planet');
            if($quest) {
                $user->removeQuest($quest);
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 250);
            }
        }

        $em->flush();
        return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition(), 'gal' => $planet->getSector()->getGalaxy()->getPosition()]);
    }
}