<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class ProductionController extends Controller
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
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getMiner() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 1;
        if(($usePlanetNb < ($level * 300) || $usePlanetWt < ($level * 100)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 300));
        $usePlanet->setWater($usePlanetWt - ($level * 100));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('miner');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getMiner();
        $newGround = $usePlanet->getGroundPlace() - 1;
        if($level == 1 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setMiner($level - 2);
        $usePlanet->setNbProduction($usePlanet->getNbProduction() - ($level / 1.1));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getExtractor() + 1;
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $newGround = $usePlanet->getGroundPlace() + 1;
        if(($usePlanetNb < ($level * 100) || $usePlanetWt < ($level * 300)) ||
            ($usePlanet->getConstructAt() > $now || $newGround > $usePlanet->getGround())) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 180) . 'S'));
        $usePlanet->setNiobium($usePlanetNb - ($level * 100));
        $usePlanet->setWater($usePlanetWt - ($level * 300));
        $usePlanet->setGroundPlace($newGround);
        $usePlanet->setConstruct('extractor');
        $usePlanet->setConstructAt($now);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $usePlanet->getExtractor() + 1;
        $newGround = $usePlanet->getGroundPlace() - 1;
        if($level == 1 || $usePlanet->getConstructAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }

        $usePlanet->setExtractor($level - 2);
        $usePlanet->setNbProduction($usePlanet->getNbProduction() - ($level / 1.05));
        $usePlanet->setGroundPlace($newGround);
        $em->persist($usePlanet);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}