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
class ArmementController extends Controller
{
    /**
     * @Route("/rechercher-armement/{idp}", name="research_armement", requirements={"idp"="\d+"})
     */
    public function researchArmementAction($idp)
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

        $armement = $user->getResearch()->getArmement();
        $userBt = $user->getBitcoin();
        if(($userBt < $armement->getBitcoin() || $armement->getFinishAt() > $now) || $armement->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $armement->getBitcoin());
        $armement->setBitcoin($armement->getBitcoin() * $cost);
        $armement->setLevel($armement->getLevel() + 1);
        $armement->setFinishAt($now);
        $armement->setConstructTime($armement->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($armement);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-missile/{idp}", name="research_missile", requirements={"idp"="\d+"})
     */
    public function researchMissileAction($idp)
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

        $missile = $user->getResearch()->getMissile();
        $userBt = $user->getBitcoin();
        if(($userBt < $missile->getBitcoin() || $missile->getFinishAt() > $now) || ($missile->getLevel() == 3 || $user->getResearch()->getArmement()->getLevel() < 0)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $missile->getBitcoin());
        $missile->setBitcoin($missile->getBitcoin() * $cost);
        $missile->setLevel($missile->getLevel() + 1);
        $missile->setFinishAt($now);
        $missile->setConstructTime($missile->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($missile);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-laser/{idp}", name="research_laser", requirements={"idp"="\d+"})
     */
    public function researchLaserAction($idp)
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

        $laser = $user->getResearch()->getLaser();
        $userBt = $user->getBitcoin();
        if(($userBt < $laser->getBitcoin() || $laser->getFinishAt() > $now) || ($laser->getLevel() == 3 || $user->getResearch()->getArmement()->getLevel() < 2)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $laser->getBitcoin());
        $laser->setBitcoin($laser->getBitcoin() * $cost);
        $laser->setLevel($laser->getLevel() + 1);
        $laser->setFinishAt($now);
        $laser->setConstructTime($laser->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($laser);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-plasma/{idp}", name="research_plasma", requirements={"idp"="\d+"})
     */
    public function researchPlasmaAction($idp)
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

        $plasma = $user->getResearch()->getPlasma();
        $userBt = $user->getBitcoin();
        if(($userBt < $plasma->getBitcoin() || $plasma->getFinishAt() > $now) || ($plasma->getLevel() == 3 || $user->getResearch()->getArmement()->getLevel() < 4)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $plasma->getBitcoin());
        $plasma->setBitcoin($plasma->getBitcoin() * $cost);
        $plasma->setLevel($plasma->getLevel() + 1);
        $plasma->setFinishAt($now);
        $plasma->setConstructTime($plasma->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($plasma);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}