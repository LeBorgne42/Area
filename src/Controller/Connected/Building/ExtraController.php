<?php

namespace App\Controller\Connected\Building;

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
class ExtraController extends AbstractController
{
    /**
     * @Route("/contruire-ile/{idp}", name="building_add_island", requirements={"idp"="\d+"})
     */
    public function buildingAddIslandAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getIsland() + 1;
        $usePlanetPdg = $user->getRank()->getWarPoint();

        if(($usePlanetPdg < ($level * 200000)) ||
            ($usePlanet->getConstructAt() > $now) ||
            $user->getExpansion() == 0) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 9000) . 'S'));
        $user->getRank()->setWarPoint($usePlanetPdg - 200000);
        $usePlanet->setConstruct('island');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-station-orbitale/{idp}", name="building_add_orbital", requirements={"idp"="\d+"})
     */
    public function buildingAddOrbitalAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getOrbital() + 1;
        $usePlanetPdg = $user->getRank()->getWarPoint();

        if(($usePlanetPdg < ($level * 200000)) ||
            ($usePlanet->getConstructAt() > $now) ||
            $user->getExpansion() < 2) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 9000) . 'S'));
        $user->getRank()->setWarPoint($usePlanetPdg - 200000);
        $usePlanet->setConstruct('orbital');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }
}