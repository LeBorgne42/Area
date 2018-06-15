<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SearchController extends Controller
{
    /**
     * @Route("/recherche/{idp}", name="search", requirements={"idp"="\d+"})
     */
    public function searchAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/search.html.twig', [
            'usePlanet' => $usePlanet,
            'date' => $now,
        ]);
    }

    /**
     * @Route("/annuler-rechercher/{idp}", name="research_cancel", requirements={"idp"="\d+"})
     */
    public function researchCancelAction($idp)
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

        $research = $user->getSearch();
        if ($research == 'onde') {
            $level = $user->getOnde() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 2300));
        } elseif ($research == 'industry') {
            $level = $user->getIndustry() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 1500));
        } elseif ($research == 'discipline') {
            $level = $user->getDiscipline() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 11700));
        } elseif ($research == 'hyperespace') {
            $level = $user->getHyperespace() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 25000000));
        } elseif ($research == 'barge') {
            $level = $user->getBarge() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 35000));
        } elseif ($research == 'utility') {
            $level = $user->getUtility() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 2500));
        } elseif ($research == 'demography') {
            $level = $user->getDemography() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 8000));
        } elseif ($research == 'terraformation') {
            $level = $user->getTerraformation() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 12000));
        } elseif ($research == 'cargo') {
            $level = $user->getCargo() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 3500));
        } elseif ($research == 'recycleur') {
            $level = $user->getRecycleur() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 16900));
        } elseif ($research == 'armement') {
            $level = $user->getArmement() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 2000));
        } elseif ($research == 'missile') {
            $level = $user->getMissile() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 2600));
        } elseif ($research == 'laser') {
            $level = $user->getLaser() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 13000));
        } elseif ($research == 'plasma') {
            $level = $user->getPlasma() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 29000));
        } elseif ($research == 'lightShip') {
            $level = $user->getLightShip() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 9000));
        } elseif ($research == 'heavyShip') {
            $level = $user->getHeavyShip() + 1;
            $user->setBitcoin($user->getBitcoin() + ($level * 42000));
        }
        $user->setSearch(null);
        $user->setSearchAt(null);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('overview', array('idp' => $usePlanet->getId()));
    }
}