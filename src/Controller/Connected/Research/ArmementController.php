<?php

namespace App\Controller\Connected\Research;

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
class ArmementController extends Controller
{
    /**
     * @Route("/rechercher-armement/{idp}", name="research_armement", requirements={"idp"="\d+"})
     */
    public function researchArmementAction($idp)
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

        $level = $user->getArmement() + 1;
        $userBt = $user->getBitcoin();
        if(($userBt < ($level * 2000)) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 3700 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('armement');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 2000));
        $em->persist($user);
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
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getMissile() + 1;
        $userBt = $user->getBitcoin();
        if(($userBt < ($level * 2600) || $user->getArmement() < 0) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 4500 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('missile');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 2600));
        $em->persist($user);
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
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getLaser() + 1;
        $userBt = $user->getBitcoin();
        if(($userBt < ($level * 13000) || $user->getArmement() < 2) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 18000 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('laser');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 13000));
        $em->persist($user);
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
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();
        $level = $user->getPlasma() + 1;
        $userBt = $user->getBitcoin();
        if(($userBt < ($level * 29000) || $user->getArmement() < 4) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $now->add(new DateInterval('PT' . ($level * 46800 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('plasma');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 29000));
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}