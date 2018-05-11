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
class SociologicController extends Controller
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
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();
        $level = $user->getDemography() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 8000)) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . round(($level * 4800 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('demography');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 8000));
        $usePlanet->setWorkerProduction($usePlanet->getWorkerProduction() + 0.1);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
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
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getDiscipline() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < ($level * 11700) || $user->getDemography() == 0) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . round(($level * 9300 / $user->getScientistProduction())) . 'S'));
        $user->setSearch('discipline');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * 11700));
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}