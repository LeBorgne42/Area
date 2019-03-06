<?php

namespace App\Controller\Connected\Research;

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
class ExplorerController extends AbstractController
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getOnde() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 2300)) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 300 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('onde');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 2300));
        if(($user->getTutorial() == 8)) {
            $user->setTutorial(9);
        }
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getTerraformation() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 12000) || $user->getUtility() == 0) ||
            ($level > 19 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 360) / $user->getScientistProduction()) . 'S')); // X100 NORMAL GAME
        $user->setSearch('terraformation');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 12000));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getCargo() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 3500) || $user->getUtility() < 2) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 400 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('cargo');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 3500));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getRecycleur();
        $userBt = $user->getBitcoin();

        if(($userBt < 16900 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(400 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('recycleur');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 16900);
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getBarge();
        $userBt = $user->getBitcoin();

        if(($userBt < 35000 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(280 / $user->getScientistProduction()) . 'S')); // X100 NORMAL GAME
        $user->setSearch('barge');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 35000);
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getUtility() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 2500)) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 200 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('utility');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 2500));
        if(($user->getTutorial() == 8)) {
            $user->setTutorial(9);
        }
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
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
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getHyperespace();
        $userBt = $user->getBitcoin();

        if(($userBt < 25000000 || $user->getUtility() < 3) ||
            ($level == 1 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(6048 / $user->getScientistProduction()) . 'S')); // X100 NORMAL GAME
        $user->setSearch('hyperespace');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 25000000);
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }
}