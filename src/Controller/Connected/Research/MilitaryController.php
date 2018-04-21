<?php

namespace App\Controller\Connected\Research;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MilitaryController extends Controller
{
    /**
     * @Route("/rechercher-industrie/{idp}", name="research_industry", requirements={"idp"="\d+"})
     */
    public function researchIndustryAction($idp)
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

        $industry = $user->getResearch()->getIndustry();
        $userBt = $user->getBitcoin();
        if(($userBt < $industry->getBitcoin() || $industry->getFinishAt() > $now) || $industry->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $industry->getBitcoin());
        $industry->setBitcoin($industry->getBitcoin() * $cost);
        $industry->setLevel($industry->getLevel() + 1);
        $industry->setFinishAt($now);
        $industry->setConstructTime($industry->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($industry);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-vaisseaux-leger/{idp}", name="research_light_ship", requirements={"idp"="\d+"})
     */
    public function researchLightShipAction($idp)
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

        $lightShip = $user->getResearch()->getLightShip();
        $userBt = $user->getBitcoin();
        if(($userBt < $lightShip->getBitcoin() || $lightShip->getFinishAt() > $now) || $lightShip->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $lightShip->getBitcoin());
        $lightShip->setBitcoin($lightShip->getBitcoin() * $cost);
        $lightShip->setLevel($lightShip->getLevel() + 1);
        $lightShip->setFinishAt($now);
        $lightShip->setConstructTime($lightShip->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($lightShip);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-vaisseaux-lourd/{idp}", name="research_heavy_ship", requirements={"idp"="\d+"})
     */
    public function researchHeavyShipAction($idp)
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

        $heavyShip = $user->getResearch()->getHeavyShip();
        $userBt = $user->getBitcoin();
        if(($userBt < $heavyShip->getBitcoin() || $heavyShip->getFinishAt() > $now) || $heavyShip->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $heavyShip->getBitcoin());
        $heavyShip->setBitcoin($heavyShip->getBitcoin() * $cost);
        $heavyShip->setLevel($heavyShip->getLevel() + 1);
        $heavyShip->setFinishAt($now);
        $heavyShip->setConstructTime($heavyShip->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($heavyShip);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}