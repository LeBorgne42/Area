<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ProductionController extends AbstractController
{
    /**
     * @Route("/contruire-mine/{idp}", name="building_add_mine", requirements={"idp"="\d+"})
     */
    public function buildingAddMineAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getMiner() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 1;
        if(($usePlanetNb < ($level * 450) || $usePlanetWt < ($level * 200)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 450));
        $usePlanet->setWater($usePlanetWt - ($level * 200));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('miner');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-mine/{idp}", name="building_remove_mine", requirements={"idp"="\d+"})
     */
    public function buildingRemoveMineAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getMiner();
        $newGround = $usePlanet->getGroundPlace() - 1;
        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 1800 . 'S'));
        $usePlanet->setMiner($level - 1);
        $usePlanet->setNbProduction($usePlanet->getNbProduction() - ($level * 1.1));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-puit/{idp}", name="building_add_extract", requirements={"idp"="\d+"})
     */
    public function buildingAddExtractAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getExtractor() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 1;
        if(($usePlanetNb < ($level * 200) || $usePlanetWt < ($level * 500)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 200));
        $usePlanet->setWater($usePlanetWt - ($level * 500));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('extractor');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-puit/{idp}", name="building_remove_extract", requirements={"idp"="\d+"})
     */
    public function buildingRemoveExtractAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getExtractor();
        $newGround = $usePlanet->getGroundPlace() - 1;
        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 1800 . 'S'));
        $usePlanet->setExtractor($level - 1);
        $usePlanet->setWtProduction($usePlanet->getWtProduction() - ($level * 1.09));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-stockage-niobium/{idp}", name="building_add_niobiumStock", requirements={"idp"="\d+"})
     */
    public function buildingAddNiobiumStockAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getNiobiumStock() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 3;
        if(($usePlanetNb < ($level * 150000) || $usePlanetWt < ($level * 100000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
        $user->getCargo() < 2) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . ($level * 21600) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 150000));
        $usePlanet->setWater($usePlanetWt - ($level * 100000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('niobiumStock');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-stockage-niobium/{idp}", name="building_remove_niobiumStock", requirements={"idp"="\d+"})
     */
    public function buildingRemoveNiobiumStockAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getNiobiumStock();
        $newGround = $usePlanet->getGroundPlace() - 3;
        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 1800 . 'S'));
        $usePlanet->setExtractor($level - 1);
        $usePlanet->setNiobiumMax($usePlanet->getNiobiumMax() - 750000);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-stockage-eau/{idp}", name="building_add_waterStock", requirements={"idp"="\d+"})
     */
    public function buildingAddWaterStockAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getWaterStock() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 3;
        if(($usePlanetNb < ($level * 110000) || $usePlanetWt < ($level * 180000)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            $user->getCargo() < 2) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . ($level * 21600) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 110000));
        $usePlanet->setWater($usePlanetWt - ($level * 180000));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('waterStock');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-stockage-eau/{idp}", name="building_remove_waterStock", requirements={"idp"="\d+"})
     */
    public function buildingRemoveWaterStockAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $level = $usePlanet->getWaterStock();
        $newGround = $usePlanet->getGroundPlace() - 3;
        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 1800 . 'S'));
        $usePlanet->setWaterMax($usePlanet->getWaterMax() - 750000);
        $usePlanet->setExtractor($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }
}