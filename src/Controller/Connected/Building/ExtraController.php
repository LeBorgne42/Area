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
class ExtraController extends AbstractController
{
    /**
     * @Route("/contruire-ile/{usePlanet}", name="building_add_island", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddIslandAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getIsland() + 1;
        $usePlanetPdg = $user->getRank()->getWarPoint();

        if(($usePlanetPdg < ($level * 200000)) ||
            ($usePlanet->getConstructAt() > $now) ||
            $user->getExpansion() == 0) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 9000) . 'S'));
        $user->getRank()->setWarPoint($usePlanetPdg - 200000);
        $usePlanet->setConstruct('island');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-station-orbitale/{usePlanet}", name="building_add_orbital", requirements={"usePlanet"="\d+"})
     */
    public function buildingAddOrbitalAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $level = $usePlanet->getOrbital() + 1;
        $usePlanetPdg = $user->getRank()->getWarPoint();

        if(($usePlanetPdg < ($level * 200000)) ||
            ($usePlanet->getConstructAt() > $now) ||
            $user->getExpansion() < 2) {
            return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 9000) . 'S'));
        $user->getRank()->setWarPoint($usePlanetPdg - 200000);
        $usePlanet->setConstruct('orbital');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['usePlanet' => $usePlanet->getId()]);
    }
}