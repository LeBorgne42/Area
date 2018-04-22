<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SpatialShipType;

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
            /*$ship = $usePlanet->getShip();*/
        } else {
            return $this->render('connected/spatial.html.twig', [
                'usePlanet' => $usePlanet,
                'form_spatialShip' => $form_spatialShip->createView(),
            ]);
        }
        if ($form_spatialShip->isSubmitted() && $form_spatialShip->isValid()) {
            /*$em->persist($ship);*/
            $colonizer = $usePlanet->getShip()->getColonizer();
            $sonde = $usePlanet->getShip()->getSonde();
            $hunter = $usePlanet->getShip()->getHunter();
            $fregate = $usePlanet->getShip()->getFregate();
            $niobiumLess = ($colonizer->getNiobium() * $form_spatialShip->get('colonizer')->getData()) + ($sonde->getNiobium() * $form_spatialShip->get('sonde')->getData()) + ($hunter->getNiobium() * $form_spatialShip->get('hunter')->getData()) + ($fregate->getNiobium() * $form_spatialShip->get('fregate')->getData());
            $waterLess =  ($colonizer->getWater() * $form_spatialShip->get('colonizer')->getData()) + ($hunter->getWater() * $form_spatialShip->get('hunter')->getData()) + ($fregate->getWater() * $form_spatialShip->get('fregate')->getData());
            $bitcoinLess = ($colonizer->getBitcoin() * $form_spatialShip->get('colonizer')->getData());
            $colonizer->setAmount($colonizer->getAmount() + $form_spatialShip->get('colonizer')->getData());
            $sonde->setAmount($sonde->getAmount() + $form_spatialShip->get('sonde')->getData());
            $hunter->setAmount($hunter->getAmount() + $form_spatialShip->get('hunter')->getData());
            $fregate->setAmount($fregate->getAmount() + $form_spatialShip->get('fregate')->getData());
            $usePlanet->setNiobium($usePlanet->getNiobium() - $niobiumLess);
            $usePlanet->setWater($usePlanet->getWater() - $waterLess);
            $user->setBitcoin($user->getBitcoin() - $bitcoinLess);
            $em->persist($colonizer);
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
}