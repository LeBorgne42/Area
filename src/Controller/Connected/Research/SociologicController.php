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
class SociologicController extends AbstractController
{
    /**
     * @Route("/rechercher-demographie/{idp}", name="research_demography", requirements={"idp"="\d+"})
     */
    public function researchDemographyAction($idp)
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
        $level = $user->getDemography() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 8000)) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 480 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('demography');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 8000));
        $usePlanet->setWorkerProduction($usePlanet->getWorkerProduction() + 500);
        if(($user->getTutorial() == 8)) {
            $user->setTutorial(9);
        }
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rechercher-discipline/{idp}", name="research_discipline", requirements={"idp"="\d+"})
     */
    public function researchDisciplineAction($idp)
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

        $level = $user->getDiscipline() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 11700) || $user->getDemography() == 0) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 930 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('discipline');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 11700));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rechercher-barbele/{idp}", name="research_barbed", requirements={"idp"="\d+"})
     */
    public function researchBarbedAction($idp)
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

        $level = $user->getBarbed() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 200000) || $user->getDiscipline() != 3) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 1200 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('barbed');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 200000));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rechercher-tank/{idp}", name="research_tank", requirements={"idp"="\d+"})
     */
    public function researchTankAction($idp)
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

        $level = $user->getTank() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 40000) || $user->getDiscipline() != 3) ||
            ($level == 2 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 2000 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('tank');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 40000));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rechercher-expansion/{idp}", name="research_expansion", requirements={"idp"="\d+"})
     */
    public function researchExpansionAction($idp)
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

        $level = $user->getExpansion() + 1;
        $userPdg = $user->getRank()->getWarPoint();

        if(($userPdg < ($level * 75000) || $user->getHyperespace() == 0) ||
            ($level == 2 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * 6000 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('expansion');
        $user->setSearchAt($now);
        $user->getRank()->setWarPoint($userPdg - ($level * 75000));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }
}