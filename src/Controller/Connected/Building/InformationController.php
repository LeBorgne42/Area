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
class InformationController extends AbstractController
{
    /**
     * @Route("/contruire-radar/{idp}", name="building_add_radar", requirements={"idp"="\d+"})
     */
    public function buildingAddRadarAction($idp)
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

        $level = $usePlanet->getRadar() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 2;

        if(($usePlanetNb < ($level * 1200) || $usePlanetWt < ($level * 650)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround()) ||
            $user->getOnde() == 0) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 2200) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 1200));
        $usePlanet->setWater($usePlanetWt - ($level * 650));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('radar');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-radar/{idp}", name="building_remove_radar", requirements={"idp"="\d+"})
     */
    public function buildingRemoveRadarAction($idp)
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

        $level = $usePlanet->getRadar();
        $newGround = $usePlanet->getGroundPlace() - 2;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setRadar($level - 1);
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-radar-espace/{idp}", name="building_add_skyRadar", requirements={"idp"="\d+"})
     */
    public function buildingSkyRadarAction($idp)
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

        $level = $usePlanet->getSkyRadar() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + 2;

        if(($usePlanetNb < ($level * 20000) || $usePlanetWt < ($level * 17200)) ||
            ($usePlanet->getConstructAt() > $now || $newSky > $usePlanet->getSky()) ||
            $user->getOnde() < 3) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 14400) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 20000));
        $usePlanet->setWater($usePlanetWt - ($level * 17200));
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('skyRadar');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-radar-espace/{idp}", name="building_remove_skyRadar", requirements={"idp"="\d+"})
     */
    public function buildingRemoveSkyRadarAction($idp)
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

        $level = $usePlanet->getSkyRadar();
        $newSky = $usePlanet->getSkyPlace() - 2;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setSkyRadar($level - 1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/contruire-brouilleur/{idp}", name="building_add_brouilleur", requirements={"idp"="\d+"})
     */
    public function buildingBrouilleurAction($idp)
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

        $level = $usePlanet->getSkyBrouilleur() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newSky = $usePlanet->getSkyPlace() + 4;

        if(($usePlanetNb < ($level * 51000) || $usePlanetWt < ($level * 32100)) ||
            ($usePlanet->getConstructAt() > $now || $newSky > $usePlanet->getSky()) ||
            $user->getOnde() < 5) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . ($level * 32400) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 51000));
        $usePlanet->setWater($usePlanetWt - ($level * 32100));
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('skyBrouilleur');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-brouilleur/{idp}", name="building_remove_brouilleur", requirements={"idp"="\d+"})
     */
    public function buildingRemoveBrouilleurAction($idp)
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

        $level = $usePlanet->getSkyBrouilleur();
        $newSky = $usePlanet->getSkyPlace() - 4;

        if($level == 0 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
        }
        $now->add(new DateInterval('PT' . 600 . 'S'));
        $usePlanet->setSkyBrouilleur($level - 1);
        $usePlanet->setSkyPlace($newSky);
        $usePlanet->setConstruct('destruct');
        $usePlanet->setConstructAt($now);
        $em->flush();

        return $this->redirectToRoute('building', ['idp' => $usePlanet->getId()]);
    }
}