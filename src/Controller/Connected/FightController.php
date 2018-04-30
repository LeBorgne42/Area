<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class FightController extends Controller
{
    /**
     * @Route("/weNeedABigBigFight/", name="fight_war")
     */
    public function fightAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $fleetsWar = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.fightAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();
        foreach ($fleetsWar as $mdr) {
        }
        exit;
    }

    /**
     * @Route("/hello-we-come-for-you/{idp}/{fleet}/", name="invader_planet", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function invaderAction(Request $request, $idp, $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $invader = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $barge = $invader->getBarge() * 2500;
        $defenser = $invader->getPlanet();
        $userDefender= $invader->getPlanet()->getUser();
        $dMilitary = $defenser->getWorker() + ($defenser->getSoldier() * 6);
        $alea = rand(5, 9);
        if($barge and $invader->getPlanet()->getUser() and $invader->getAllianceUser() == null) {
            if($barge >= $invader->getSoldier()) {
                $aMilitary = $invader->getSoldier() * $alea;
            } else {
                $aMilitary = $barge * $alea;
            }
            if($dMilitary > $aMilitary) {
                $aMilitary = ($defenser->getSoldier() * 6) - $aMilitary;
                $invader->setSoldier(0);
                $defenser->setBarge($defenser->getBarge() + $invader->getBarge());
                $invader->setBarge(0);
                if($aMilitary < 0) {
                    $defenser->setSoldier(0);
                    $defenser->setWorker($defenser->getWorker() + $aMilitary);
                } else {
                    $defenser->setSoldier($aMilitary / 6);
                }
            } else {
                $invader->setSoldier(($aMilitary - $dMilitary) / $alea);
                $defenser->setSoldier(0);
                $defenser->setWorker(2000);
                if(count($invader->getUser()->getPlanets()) < 21) {
                    $defenser->setUser($invader->getUser());
                } else {
                    $defenser->setUser(null);
                    $defenser->setName('Abandonnée');
                }
                if(count($userDefender->getPlanets()) == 1) {
                    $userDefender->setGameOver(true);
                    $userDefender->setAlly(null);
                    $userDefender->setGrade(null);
                    foreach($userDefender->getFleets() as $tmpFleet) {
                        $tmpFleet->setUser($invader->getUser());
                        $em->persist($tmpFleet);
                    }
                    $em->persist($userDefender);
                }
            }
            $em->persist($invader);
            if($invader->getNbrShips() == 0) {
                $em->remove($invader);
            }
            $em->persist($defenser);
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $defenser->getSector()->getPosition()));
    }

    /**
     * @Route("/colonisation-planete/{idp}/{fleet}/", name="colonizer_planet", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function colonizeAction(Request $request, $idp, $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $colonize = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $newPlanet = $colonize->getPlanet();
        if($colonize->getColonizer() && $newPlanet->getUser() == null &&
            $newPlanet->getEmpty() == false && $newPlanet->getMerchant() == false &&
            $newPlanet->getCdr() == false && count($colonize->getUser()->getPlanets()) < 21) {
            $colonize->setColonizer($colonize->getColonizer() - 1);
            $newPlanet->setUser($colonize->getUser());
            $newPlanet->setName('Colonie');
            $em->persist($colonize);
            if($colonize->getNbrShips() == 0) {
                $em->remove($colonize);
            }
            $em->persist($newPlanet);
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $newPlanet->getSector()->getPosition()));
    }
}