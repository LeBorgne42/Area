<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class BuildingController extends Controller
{
    /**
     * @Route("/batiment/{idp}", name="building", requirements={"idp"="\d+"})
     */
    public function buildingAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        return $this->render('connected/building.html.twig', [
            'usePlanet' => $usePlanet,
            'date' => $now,
        ]);
    }

    /**
     * @Route("/annuler-construction/{idp}/{id}", name="cancel_construction", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function cancelConstructionAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);


        $cancelPlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();


        $build = $cancelPlanet->getConstruct();
        if ($build == 'destruct') {
            return $this->redirectToRoute('overview', ['idp' => $usePlanet->getId()]);
        } elseif ($build == 'miner') {
            $level = $cancelPlanet->getMiner() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 225));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 100));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 2);
        } elseif ($build == 'extractor') {
            $level = $cancelPlanet->getExtract() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 100));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 250));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 3);
        } elseif ($build == 'niobiumStock') {
            $level = $cancelPlanet->getNiobiumStock() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 75000));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 50000));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 3);
        } elseif ($build == 'waterStock') {
            $level = $cancelPlanet->getWaterStock() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 55000));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 90000));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 4);
        } elseif ($build == 'city') {
            $level = $cancelPlanet->getCity() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 7500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 5500));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 6);
        } elseif ($build == 'metropole') {
            $level = $cancelPlanet->getMetropole() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 36500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 27500));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 6);
            $cancelPlanet->setSkyPlace($cancelPlanet->getSkyPlace() - 6);
        } elseif ($build == 'caserne') {
            $level = $cancelPlanet->getCaserne() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 6500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 9500));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 6);
        } elseif ($build == 'bunker') {
            $level = $cancelPlanet->getBunker() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 100000));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 95000));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 10);
        } elseif ($build == 'centerSearch') {
            $level = $cancelPlanet->getCenterSearch() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 1400));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 3500));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 5);
        } elseif ($build == 'lightUsine') {
            $level = $cancelPlanet->getLightUsine() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 3500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 1900));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 6);
        } elseif ($build == 'heavyUsine') {
            $level = $cancelPlanet->getHeavyUsine() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 41500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 34000));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 12);
        } elseif ($build == 'spaceShip') {
            $level = $cancelPlanet->getSpaceShip() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 1500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 1000));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 10);
            $cancelPlanet->setSkyPlace($cancelPlanet->getSkyPlace() - 4);
        } elseif ($build == 'radar') {
            $level = $cancelPlanet->getRadar() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 600));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 300));
            $cancelPlanet->setGroundPlace($cancelPlanet->getGroundPlace() - 2);
        } elseif ($build == 'skyRadar') {
            $level = $cancelPlanet->getSkyRadar() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 10000));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 8600));
            $cancelPlanet->setSkyPlace($cancelPlanet->getSkyPlace() - 2);
        } elseif ($build == 'skyBrouilleur') {
            $level = $cancelPlanet->getSkyBrouilleur() + 1;
            $cancelPlanet->setNiobium($cancelPlanet->getNiobium() + ($level * 25500));
            $cancelPlanet->setWater($cancelPlanet->getWater() + ($level * 16000));
            $cancelPlanet->setSkyPlace($cancelPlanet->getSkyPlace() - 4);
        }
        $cancelPlanet->setConstruct(null);
        $cancelPlanet->setConstructAt(null);
        $em->flush();

        return $this->redirectToRoute('overview', ['idp' => $usePlanet->getId()]);
    }
}