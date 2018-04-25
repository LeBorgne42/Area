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
    public function bitcoinLoadAction()
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
                $niobium = $niobium + ($planet->getBuilding()->getMiner()->getProduction());
                $water = $water + ($planet->getBuilding()->getExtractor()->getProduction());
                $planet->setNiobium($niobium);
                $planet->setWater($water);
                $em->persist($planet);
                /*if($planet->getBuilding()->getConstruct() < $now) {
                    $building = $planet->getBuilding();
                    if($building->getMiner()->getFinishAt()) {
                        $building->getMiner()->setLevel($building->getMiner()->getLevel() + 1);
                        $building->getMiner()->setFinishAt(null);
                    }
                    if($building->getExtractor()->getFinishAt()) {
                        $building->getExtractor()->setLevel($building->getExtractor()->getLevel() + 1);
                        $building->getExtractor()->setFinishAt(null);
                    }
                    if($building->getMetropole()->getFinishAt()) {
                        $building->getMetropole()->setLevel($building->getMetropole()->getLevel() + 1);
                        $building->getMetropole()->setFinishAt(null);
                    }
                    if($building->getCity()->getFinishAt()) {
                        $building->getCity()->setLevel($building->getCity()->getLevel() + 1);
                        $building->getCity()->setFinishAt(null);
                    }
                    if($building->getCaserne()->getFinishAt()) {
                        $building->getCaserne()->setLevel($building->getCaserne()->getLevel() + 1);
                        $building->getCaserne()->setFinishAt(null);
                    }
                    if($building->getRadar()->getFinishAt()) {
                        $building->getRadar()->setLevel($building->getRadar()->getLevel() + 1);
                        $building->getRadar()->setFinishAt(null);
                    }
                    if($building->getSpaceShip()->getFinishAt()) {
                        $building->getSpaceShip()->setLevel($building->getSpaceShip()->getLevel() + 1);
                        $building->getSpaceShip()->setFinishAt(null);
                    }
                    if($building->getBuildSearch()->getFinishAt()) {
                        $building->getBuildSearch()->setLevel($building->getBuildSearch()->getLevel() + 1);
                        $building->getBuildSearch()->setFinishAt(null);
                    }
                    if($building->getSkyRadar()->getFinishAt()) {
                        $building->getSkyRadar()->setLevel($building->getSkyRadar()->getLevel() + 1);
                        $building->getSkyRadar()->setFinishAt(null);
                    }
                    if($building->getSkyBrouilleur()->getFinishAt()) {
                        $building->getSkyBrouilleur()->setLevel($building->getSkyBrouilleur()->getLevel() + 1);
                        $building->getSkyBrouilleur()->setFinishAt(null);
                    }
                    if($building->getLightUsine()->getFinishAt()) {
                        $building->getLightUsine()->setLevel(1);
                        $building->getLightUsine()->setFinishAt(null);
                    }
                    if($building->getHeavyUsine()->getFinishAt()) {
                        $building->getHeavyUsine()->setLevel(1);
                        $building->getHeavyUsine()->setFinishAt(null);
                    }
                    $building->setConstruct(null);
                    $em->persist($building);
                    $em->flush();
                }*/
            }
            /*if($user->getSearch() < $now) {
                $research = $user->getResearch();
                if($research->getOnde()->getFinishAt()) {
                    $research->getOnde()->setLevel($research->getOnde()->getLevel() + 1);
                    $research->getOnde()->setFinishAt(null);
                }
                if($research->getIndustry()->getFinishAt()) {
                    $research->getIndustry()->setLevel($research->getIndustry()->getLevel() + 1);
                    $research->getIndustry()->setFinishAt(null);
                }
                if($research->getDiscipline()->getFinishAt()) {
                    $research->getDiscipline()->setLevel($research->getDiscipline()->getLevel() + 1);
                    $research->getDiscipline()->setFinishAt(null);
                }
                if($research->getHyperespace()->getFinishAt()) {
                    $research->getHyperespace()->setLevel(1);
                    $research->getHyperespace()->setFinishAt(null);
                }
                if($research->getBarge()->getFinishAt()) {
                    $research->getBarge()->setLevel(1);
                    $research->getBarge()->setFinishAt(null);
                }
                if($research->getUtility()->getFinishAt()) {
                    $research->getUtility()->setLevel($research->getUtility()->getLevel() + 1);
                    $research->getUtility()->setFinishAt(null);
                }
                if($research->getDemography()->getFinishAt()) {
                    $research->getDemography()->setLevel($research->getDemography()->getLevel() + 1);
                    $research->getDemography()->setFinishAt(null);
                }
                if($research->getTerraformation()->getFinishAt()) {
                    $research->getTerraformation()->setLevel(1);
                    $research->getTerraformation()->setFinishAt(null);
                }
                if($research->getCargo()->getFinishAt()) {
                    $research->getCargo()->setLevel($research->getCargo()->getLevel() + 1);
                    $research->getCargo()->setFinishAt(null);
                }
                if($research->getRecycleur()->getFinishAt()) {
                    $research->getRecycleur()->setLevel(1);
                    $research->getRecycleur()->setFinishAt(null);
                }
                if($research->getArmement()->getFinishAt()) {
                    $research->getArmement()->setLevel($research->getArmement()->getLevel() + 1);
                    $research->getArmement()->setFinishAt(null);
                }
                if($research->getMissile()->getFinishAt()) {
                    $research->getMissile()->setLevel($research->getMissile()->getLevel() + 1);
                    $research->getMissile()->setFinishAt(null);
                }
                if($research->getLaser()->getFinishAt()) {
                    $research->getLaser()->setLevel($research->getLaser()->getLevel() + 1);
                    $research->getLaser()->setFinishAt(null);
                }
                if($research->getPlasma()->getFinishAt()) {
                    $research->getPlasma()->setLevel($research->getPlasma()->getLevel() + 1);
                    $research->getPlasma()->setFinishAt(null);
                }
                if($research->getLightShip()->getFinishAt()) {
                    $research->getLightShip()->setLevel($research->getLightShip()->getLevel() + 1);
                    $research->getLightShip()->setFinishAt(null);
                }
                if($research->getHeavyShip()->getFinishAt()) {
                    $research->getHeavyShip()->setLevel($research->getHeavyShip()->getLevel() + 1);
                    $research->getHeavyShip()->setFinishAt(null);
                }
                $user->setSearch(null);
                $em->persist($user);
                $em->persist($research);
                $em->flush();
            }*/
            $bitcoin = $user->getBitcoin();
            $bitcoin = $bitcoin + ($worker / 1400);
            $user->setBitcoin($bitcoin);
            $em->persist($user);
        }
        $em->flush();

        exit;
    }
}
