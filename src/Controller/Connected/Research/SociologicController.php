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

        $demography = $user->getResearch()->getDemography();
        $userBt = $user->getBitcoin();
        if(($userBt < $demography->getBitcoin() || $demography->getFinishAt() > $now) ||
            ($demography->getLevel() == 5 || $user->getSearch() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;
        $now->add(new DateInterval('PT' . $demography->getConstructTime() . 'S'));
        $user->setSearch($now);
        $user->setBitcoin($userBt - $demography->getBitcoin());
        $demography->setBitcoin($demography->getBitcoin() * $cost);
        $demography->setFinishAt($now);
        $demography->setConstructTime($demography->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($demography);
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

        $discipline = $user->getResearch()->getDiscipline();
        $userBt = $user->getBitcoin();
        if(($userBt < $discipline->getBitcoin() || $discipline->getFinishAt() > $now) ||
            ($discipline->getLevel() == 3 || $user->getResearch()->getDemography()->getLevel() == 0) ||
            $user->getSearch() > $now) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;
        $now->add(new DateInterval('PT' . $discipline->getConstructTime() . 'S'));
        $user->setSearch($now);
        $user->setBitcoin($userBt - $discipline->getBitcoin());
        $discipline->setBitcoin($discipline->getBitcoin() * $cost);
        $discipline->setFinishAt($now);
        $discipline->setConstructTime($discipline->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($discipline);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}