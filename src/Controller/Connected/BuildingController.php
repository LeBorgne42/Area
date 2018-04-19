<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/building.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/contruire-mine/{idp}", name="building_add_mine", requirements={"idp"="\d+"})
     */
    public function buildingAddMineAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $miner = $usePlanet->getBuilding()->getMiner();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        if(($usePlanetNb < $miner->getNiobium() || $usePlanetWt < $miner->getWater()) || $miner->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $miner->setNiobium($miner->getNiobium() * 1.5);
        $miner->setWater($miner->getWater() * 1.5);
        $miner->setProduction($miner->getProduction() * 1.4);
        $miner->setLevel($miner->getLevel() + 1);
        $miner->setFinishAt($now);
        $miner->setConstructTime($miner->getConstructTime() * 1.8);
        $usePlanet->setNiobium($usePlanetNb - $miner->getNiobium());
        $usePlanet->setWater($usePlanetWt - $miner->getWater());
        $em->persist($miner);
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
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $miner = $usePlanet->getBuilding()->getMiner();
        if($miner->getLevel() == 1 || $miner->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $miner->setNiobium($miner->getNiobium() / 1.5);
        $miner->setWater($miner->getWater() / 1.5);
        $miner->setProduction($miner->getProduction() / 1.4);
        $miner->setLevel($miner->getLevel() - 1);
        $miner->setFinishAt($now);
        $miner->setConstructTime($miner->getConstructTime() / 1.8);
        $usePlanet->setNiobium($usePlanetNb + ($miner->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($miner->getWater() / 1.5));
        $em->persist($miner);
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
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $extract = $usePlanet->getBuilding()->getExtractor();
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        if(($usePlanetNb < $extract->getNiobium() || $usePlanetWt < $extract->getWater()) || $extract->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $extract->setNiobium($extract->getNiobium() * 1.5);
        $extract->setWater($extract->getWater() * 1.5);
        $extract->setProduction($extract->getProduction() * 1.4);
        $extract->setLevel($extract->getLevel() + 1);
        $extract->setFinishAt($now);
        $extract->setConstructTime($extract->getConstructTime() * 1.8);
        $usePlanet->setNiobium($usePlanetNb - $extract->getNiobium());
        $usePlanet->setWater($usePlanetWt - $extract->getWater());
        $em->persist($extract);
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
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $extract = $usePlanet->getBuilding()->getExtractor();
        if($extract->getLevel() == 1 || $extract->getFinishAt() > $now) {
            return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
        }
        $usePlanetNb = $usePlanet->getNiobium();
        $usePlanetWt = $usePlanet->getWater();
        $extract->setNiobium($extract->getNiobium() / 1.5);
        $extract->setWater($extract->getWater() / 1.5);
        $extract->setProduction($extract->getProduction() / 1.4);
        $extract->setLevel($extract->getLevel() - 1);
        $extract->setFinishAt($now);
        $extract->setConstructTime($extract->getConstructTime() / 1.8);
        $usePlanet->setNiobium($usePlanetNb + ($extract->getNiobium() / 1.5));
        $usePlanet->setWater($usePlanetWt + ($extract->getWater() / 1.5));
        $em->persist($extract);
        $em->flush();

        return $this->redirectToRoute('building', array('idp' => $usePlanet->getId()));
    }
}