<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class InformationController extends AbstractController
{
    /**
     * @Route("/contruire-radar/{usePlanet}", name="building_add_radar", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddRadarAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getRadar() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 2;

        if(($usePlanetNb < ($level * 1200) || $usePlanetWt < ($level * 650)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            $user->getOnde() == 0) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 220) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 1200));
        $usePlanet->setWater($usePlanetWt - ($level * 650));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('radar');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-radar/{usePlanet}", name="building_remove_radar", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveRadarAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getRadar();
        $newGround = $usePlanet->getGroundPlace() - 2;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setRadar($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-radar-espace/{usePlanet}", name="building_add_skyRadar", requirements={"usePlanet"="\d+"})
     */
    public function buildingSkyRadarAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSkyRadar() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + 2;

        if(($usePlanetNb < ($level * 20000) || $usePlanetWt < ($level * 17200)) ||
            ($usePlanet->getConstructAt() > $now || $newSky > $usePlanet->getSky()) ||
            $user->getOnde() < 3) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 1440) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 20000));
        $usePlanet->setWater($usePlanetWt - ($level * 17200));
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('skyRadar');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-radar-espace/{usePlanet}", name="building_remove_skyRadar", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveSkyRadarAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSkyRadar();
        $newSky = $usePlanet->getSkyPlace() - 2;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setSkyRadar($level - 1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-brouilleur/{usePlanet}", name="building_add_brouilleur", requirements={"usePlanet"="\d+"})
     */
    public function buildingBrouilleurAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSkyBrouilleur() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + 4;

        if(($usePlanetNb < ($level * 51000) || $usePlanetWt < ($level * 32100)) ||
            ($usePlanet->getConstructAt() > $now || $newSky > $usePlanet->getSky()) ||
            $user->getOnde() < 5) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 3240) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 51000));
        $usePlanet->setWater($usePlanetWt - ($level * 32100));
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('skyBrouilleur');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-brouilleur/{usePlanet}", name="building_remove_brouilleur", requirements={"usePlanet"="\d+"})
     */
    public function buildingRemoveBrouilleurAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getSkyBrouilleur();
        $newSky = $usePlanet->getSkyPlace() - 4;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 180 . 'S'));
        $usePlanet->setSkyBrouilleur($level - 1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }
}