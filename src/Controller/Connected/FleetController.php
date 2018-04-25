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

            $fleet->setName($form_manageFleet->get('name')->getData());
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

        if($fleet || $usePlanet) {
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
            $colonizer = $usePlanet->getColonizer() - $form_manageFleet->get('moreColonizer')->getData();
            $recycleur = $usePlanet->getRecycleur() - $form_manageFleet->get('moreRecycleur')->getData();
            $barge = $usePlanet->getBarge() - $form_manageFleet->get('moreBarge')->getData();
            $sonde = $usePlanet->getSonde() - $form_manageFleet->get('moreSonde')->getData();
            $hunter = $usePlanet->getHunter() - $form_manageFleet->get('moreHunter')->getData();
            $fregate = $usePlanet->getFregate() - $form_manageFleet->get('moreFregate')->getData();
            $colonizerLess = $usePlanet->getColonizer() + $form_manageFleet->get('lessColonizer')->getData();
            $recycleurLess = $usePlanet->getRecycleur() + $form_manageFleet->get('lessRecycleur')->getData();
            $bargeLess = $usePlanet->getBarge() + $form_manageFleet->get('lessBarge')->getData();
            $sondeLess = $usePlanet->getSonde() + $form_manageFleet->get('lessSonde')->getData();
            $hunterLess = $usePlanet->getHunter() + $form_manageFleet->get('lessHunter')->getData();
            $fregateLess = $usePlanet->getFregate() + $form_manageFleet->get('lessFregate')->getData();

            if (($colonizer < 0 || $recycleur < 0) || ($barge < 0 || $sonde < 0) || ($hunter < 0 || $fregate < 0) ||
                ($form_manageFleet->get('lessColonizer')->getData() > $fleet->getColonizer ||
                    $form_manageFleet->get('lessRecycleur')->getData() > $fleet->getRecycleur) ||
                ($form_manageFleet->get('lessBarge')->getData() > $fleet->getBarge ||
                    $form_manageFleet->get('lessSonde')->getData() > $fleet->getSonde) ||
                ($form_manageFleet->get('lessHunter')->getData() > $fleet->getHunter ||
                    $form_manageFleet->get('lessFregate')->getData() > $fleet->getFregate)) {
                return $this->redirectToRoute('fleet', array('idp' => $usePlanet->getId()));
            }
            $ship = $fleet;
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
            $usePlanet->getColonizer($usePlanet->getColonizer() - $colonizer);
            $usePlanet->getRecycleur($usePlanet->getRecycleur() - $recycleur);
            $usePlanet->getBarge($usePlanet->getBarge() - $barge);
            $usePlanet->getSonde($usePlanet->getSonde() - $sonde);
            $usePlanet->getHunter($usePlanet->getHunter() - $hunter);
            $usePlanet->getFregate($usePlanet->getFregate() - $fregate);
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