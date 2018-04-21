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
class ExplorerController extends Controller
{
    /**
     * @Route("/rechercher-onde/{idp}", name="research_onde", requirements={"idp"="\d+"})
     */
    public function researchOndeAction($idp)
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

        $onde = $user->getResearch()->getOnde();
        $userBt = $user->getBitcoin();
        if(($userBt < $onde->getBitcoin() || $onde->getFinishAt() > $now) || $onde->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $onde->getBitcoin());
        $onde->setBitcoin($onde->getBitcoin() * $cost);
        $onde->setLevel($onde->getLevel() + 1);
        $onde->setFinishAt($now);
        $onde->setConstructTime($onde->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($onde);
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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $terraformation = $user->getResearch()->getTerraformation();
        $userBt = $user->getBitcoin();
        if(($userBt < $terraformation->getBitcoin() || $terraformation->getFinishAt() > $now) || $terraformation->getLevel() == 1) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $user->setBitcoin($userBt - $terraformation->getBitcoin());
        $terraformation->setLevel($terraformation->getLevel() + 1);
        $terraformation->setFinishAt($now);
        $em->persist($user);
        $em->persist($terraformation);
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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $cargo = $user->getResearch()->getCargo();
        $userBt = $user->getBitcoin();
        if(($userBt < $cargo->getBitcoin() || $cargo->getFinishAt() > $now) || $cargo->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $cargo->getBitcoin());
        $cargo->setBitcoin($cargo->getBitcoin() * $cost);
        $cargo->setLevel($cargo->getLevel() + 1);
        $cargo->setFinishAt($now);
        $cargo->setConstructTime($cargo->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($cargo);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-recyclage/{idp}", name="research_recyclage", requirements={"idp"="\d+"})
     */
    public function researchRecyclagenAction($idp)
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

        $recyclage = $user->getResearch()->getRecycleur();
        $userBt = $user->getBitcoin();
        if(($userBt < $recyclage->getBitcoin() || $recyclage->getFinishAt() > $now) || $recyclage->getLevel() == 1) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $user->setBitcoin($userBt - $recyclage->getBitcoin());
        $recyclage->setLevel($recyclage->getLevel() + 1);
        $recyclage->setFinishAt($now);
        $em->persist($user);
        $em->persist($recyclage);
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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $utility = $user->getResearch()->getUtility();
        $userBt = $user->getBitcoin();
        if(($userBt < $utility->getBitcoin() || $utility->getFinishAt() > $now) || $utility->getLevel() == 5) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }
        $cost = 2;
        $time = 2;

        $user->setBitcoin($userBt - $utility->getBitcoin());
        $utility->setBitcoin($utility->getBitcoin() * $cost);
        $utility->setLevel($utility->getLevel() + 1);
        $utility->setFinishAt($now);
        $utility->setConstructTime($utility->getConstructTime() * $time);
        $em->persist($user);
        $em->persist($utility);
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
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $hyperespace = $user->getResearch()->getHyperespace();
        $userBt = $user->getBitcoin();
        if(($userBt < $hyperespace->getBitcoin() || $hyperespace->getFinishAt() > $now) || $hyperespace->getLevel() == 1) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $user->setBitcoin($userBt - $hyperespace->getBitcoin());
        $hyperespace->setLevel($hyperespace->getLevel() + 1);
        $hyperespace->setFinishAt($now);
        $em->persist($user);
        $em->persist($hyperespace);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}