<?php

namespace App\Controller\CronController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DateTime;
use DateTimeZone;

class RessourcesController extends Controller
{
    /**
     * @Route("/medisdfssSDFdfda37jnhb=&dgsgJHJH=UJHBJ=8==75IHBJHKNj=&knbuih6=89&738=&273&74dsffDF990&7=64&7Y/", name="ressources_load")
     */
    public function minuteLoadAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $worker = 0;
            foreach ($user->getPlanets() as $planet) {
                $worker = $worker + $planet->getWorker();
                $niobium = $planet->getNiobium();
                $water = $planet->getWater();
                $niobium = $niobium + ($planet->getNbProduction());
                $water = $water + ($planet->getWtProduction());
                $planet->setNiobium($niobium);
                $planet->setWater($water);
                $build = $planet->getConstruct();
                if($planet->getConstructAt() < $now) {
                    if($build == 'miner') {
                        $planet->setMiner($planet->getMiner() + 1);
                    } elseif ($build == 'extractor') {
                        $planet->setExtractor($planet->getExtractor() + 1);
                    } elseif ($build == 'city') {
                        $planet->setCity($planet->getCity() + 1);
                    } elseif ($build == 'metropole') {
                        $planet->setMetropole($planet->getMetropole() + 1);
                    } elseif ($build == 'caserne') {
                        $planet->setCaserne($planet->getCaserne() + 1);
                    } elseif ($build == 'centerSearch') {
                        $planet->setCenterSearch($planet->getCenterSearch() + 1);
                    } elseif ($build == 'lightUsine') {
                        $planet->setLightUsine($planet->getLightUsine() + 1);
                    } elseif ($build == 'heavyUsine') {
                        $planet->setHeavyUsine($planet->getHeavyUsine() + 1);
                    } elseif ($build == 'spaceShip') {
                        $planet->setSpaceShip($planet->getSpaceShip() + 1);
                    } elseif ($build == 'radar') {
                        $planet->setRadar($planet->getRadar() + 1);
                    } elseif ($build == 'skyRadar') {
                        $planet->setSkyRadar($planet->getSkyRadar() + 1);
                    } elseif ($build == 'skyBrouilleur') {
                        $planet->setSkyBrouilleur($planet->getSkyBrouilleur() + 1);
                    }
                    $planet->setConstruct(null);
                    $planet->setConstructAt(null);
                }
                $em->persist($planet);
            }
            $research = $user->getSearch();
            if($user->getSearchAt() < $now) {
                if($research == 'onde') {
                    $user->getOnde()($user->getOnde() + 1);
                } elseif($research == 'industry') {
                    $user->getIndustry($user->getIndustry() + 1);
                } elseif($research == 'discipline') {
                    $user->getDiscipline($user->getDiscipline() + 1);
                } elseif($research == 'hyperespace') {
                    $user->getHyperespace(1);
                } elseif($research == 'barge') {
                    $user->getBarge(1);
                } elseif($research == 'utility') {
                    $user->getUtility($user->getUtility() + 1);
                } elseif($research == 'demography') {
                    $user->getDemography($user->getDemography() + 1);
                } elseif($research == 'terraformation') {
                    $user->getTerraformation(1);
                } elseif($research == 'cargo') {
                    $user->getCargo($user->getCargo() + 1);
                } elseif($research == 'recycleur') {
                    $user->getRecycleur(1);
                } elseif($research == 'armement') {
                    $user->getArmement($user->getArmement() + 1);
                } elseif($research == 'missile') {
                    $user->getMissile($user->getMissile() + 1);
                } elseif($research == 'laser') {
                    $user->getLaser($user->getLaser() + 1);
                } elseif($research == 'plasma') {
                    $user->getPlasma($user->getPlasma() + 1);
                } elseif($research == 'lightShip') {
                    $user->getLightShip($user->getLightShip() + 1);
                } elseif($research == 'heavyShip') {
                    $user->getHeavyShip($user->getHeavyShip() + 1);
                }
                $user->setSearch(null);
                $user->setSearchAt(null);
            }
            $bitcoin = $user->getBitcoin();
            $bitcoin = $bitcoin + ($worker / 1400);
            $user->setBitcoin($bitcoin);
            $em->persist($user);
        }
        $em->flush();

        exit;
    }

    /**
     * @Route("/testit/", name="mdr")
     */
    public function plopAction()
    {
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
    var_dump($now);
        exit;
    }
}
