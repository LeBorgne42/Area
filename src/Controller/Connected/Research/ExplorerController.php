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
class ExplorerController extends Controller
{
    /**
     * @Route("/rechercher-onde/{idp}", name="research_onde", requirements={"idp"="\d+"})
     */
    public function researchOndeAction($idp)
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

        $level = $user->getOnde() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 2300)) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 3000 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('onde');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 2300));
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-terraformation/{idp}", name="research_terraformation", requirements={"idp"="\d+"})
     */
    public function researchTerraformationAction($idp)
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

        $level = $user->getTerraformation();
        $userBt = $user->getBitcoin();

        if(($userBt < 12000 || $user->getUtility() == 0) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . 3800 / $user->getScientistProduction() . 'S'));
        $user->setSearch('terraformation');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 12000);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-cargo/{idp}", name="research_cargo", requirements={"idp"="\d+"})
     */
    public function researchCargoAction($idp)
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

        $level = $user->getCargo() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 3500) || $user->getUtility() < 2) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 4000 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('cargo');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 3500));
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-recyclage/{idp}", name="research_recyclage", requirements={"idp"="\d+"})
     */
    public function researchRecyclageAction($idp)
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

        $level = $user->getRecycleur();
        $userBt = $user->getBitcoin();

        if(($userBt < 16900 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . 4000 / $user->getScientistProduction() . 'S'));
        $user->setSearch('recycleur');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 16900);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-barge/{idp}", name="research_barge", requirements={"idp"="\d+"})
     */
    public function researchBargeAction($idp)
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

        $level = $user->getBarge();
        $userBt = $user->getBitcoin();

        if(($userBt < 35000 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . 28800 / $user->getScientistProduction() . 'S'));
        $user->setSearch('barge');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 35000);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-utilitaire/{idp}", name="research_utility", requirements={"idp"="\d+"})
     */
    public function researchUtilityAction($idp)
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

        $level = $user->getUtility() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < 2500) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 2000 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('utility');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 2500);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-hyperespace/{idp}", name="research_hyperespace", requirements={"idp"="\d+"})
     */
    public function researchHyperespaceAction($idp)
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

        $level = $user->getHyperespace();
        $userBt = $user->getBitcoin();

        if(($userBt < 5000000 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . 604800 / $user->getScientistProduction() . 'S'));
        $user->setSearch('hyperespace');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 5000000);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}