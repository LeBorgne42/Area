<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use App\Entity\Sector;
use App\Entity\Galaxy;
use App\Entity\Building;
use App\Entity\Xuilding_Miner;
use App\Entity\Xuilding_Extractor;
use App\Entity\Xuilding_Caserne;
use App\Entity\Xuilding_HeavyUsine;
use App\Entity\Xuilding_LightUsine;
use App\Entity\Xuilding_Radar;
use App\Entity\Xuilding_SkyRadar;
use App\Entity\Xuilding_Search;
use App\Entity\Xuilding_SkyBrouilleur;
use App\Entity\Xuilding_SpaceShipyard;
use App\Entity\Xuilding_Metropole;
use App\Entity\Xuilding_City;
use App\Entity\Soldier;
use App\Entity\Worker;
use App\Entity\Scientist;

/**
 * @Route("/serveur")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ServerController extends Controller
{
    /**
     * @Route("/creation-serveur", name="create")
     * @Route("/creation-serveur/", name="create_withSlash")
     */
    public function createServerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nbrSector = 1;
        $nbrPlanets = 0;
        $image = ['planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png', 'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png', 'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png', 'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png', 'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png', 'planet31.png', 'planet32.png', 'planet33.png', 'planet34.png', 'planet35.png', 'planet36.png'];
        $galaxy = new Galaxy();
        $galaxy->setPosition(1);
        $em->persist($galaxy);

        while($nbrSector <= 100) {
            $nbrPlanet = 1;
            $sector = new Sector();
            $sector->setGalaxy($galaxy);
            $sector->setPosition($nbrSector);
            $em->persist($sector);
            while($nbrPlanet <= 25) {
                if(rand(1, 20) < 6) {
                    $planet = new Planet();
                    $planet->setEmpty(true);
                    $planet->setSector($sector);
                    $planet->setPosition($nbrPlanet);
                } else {
                    $nbrPlanets++;
                    $planet = new Planet();
                    $soldier = new Soldier();
                    $worker = new Worker();
                    $scientist = new Scientist();
                    $building = new Building();
                    $miner = new Xuilding_Miner();
                    $extractor = new Xuilding_Extractor();
                    $spaceShip = new Xuilding_SpaceShipyard();
                    $SkyBrouilleur = new Xuilding_SkyBrouilleur();
                    $search = new Xuilding_Search();
                    $SkyRadar = new Xuilding_SkyRadar();
                    $radar = new Xuilding_Radar();
                    $caserne = new Xuilding_Caserne();
                    $heavy = new Xuilding_HeavyUsine();
                    $light = new Xuilding_LightUsine();
                    $metropole = new Xuilding_Metropole();
                    $city = new Xuilding_City();

                    $building->setMiner($miner);
                    $building->setExtractor($extractor);
                    $building->setSpaceShip($spaceShip);
                    $building->setSkyBrouilleur($SkyBrouilleur);
                    $building->setbuildSearch($search);
                    $building->setSkyRadar($SkyRadar);
                    $building->setRadar($radar);
                    $building->setCaserne($caserne);
                    $building->setHeavyUsine($heavy);
                    $building->setLightUsine($light);
                    $building->setMetropole($metropole);
                    $building->setCity($city);
                    $soldier->setPlanet($planet);
                    $worker->setPlanet($planet);
                    $scientist->setPlanet($planet);
                    $soldier->setAmount(500);
                    $worker->setAmount(10000);
                    $scientist->setAmount(200);
                    $planet->setSoldier($soldier);
                    $planet->setWorker($worker);
                    $planet->setScientist($scientist);
                    $planet->setBuilding($building);

                    $em->persist($building);
                    $em->persist($spaceShip);
                    $em->persist($SkyBrouilleur);
                    $em->persist($search);
                    $em->persist($SkyRadar);
                    $em->persist($radar);
                    $em->persist($caserne);
                    $em->persist($heavy);
                    $em->persist($light);
                    $em->persist($metropole);
                    $em->persist($city);
                    $em->persist($miner);
                    $em->persist($extractor);
                    $em->persist($soldier);
                    $em->persist($scientist);
                    $em->persist($worker);

                    $planet->setName('Inhabitée');
                    $planet->setImageName($image[rand(0, 35)]);
                    $planet->setSector($sector);
                    $planet->setPosition($nbrPlanet);
                    if (($nbrSector >= 1 && $nbrSector <= 9) || ($nbrSector >= 92 && $nbrSector <= 99) || ($nbrSector % 10 == 0 && $nbrSector % 10 == 1)) {
                        if ($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                            $planet->setGround(60);
                            $planet->setSky(10);
                        } else {
                            $planet->setGround(rand(75, 95));
                            $planet->setSky(rand(4, 15));
                        }
                    } elseif ($nbrSector == 55 || $nbrSector == 56 || $nbrSector == 65 || $nbrSector == 66) {
                        $planet->setGround(rand(120, 160));
                        $planet->setSky(rand(3, 20));
                    } else {
                        $planet->setGround(rand(85, 125));
                        $planet->setSky(rand(6, 30));
                    }
                }
                $em->persist($planet);
                $nbrPlanet++;
            }
            $nbrSector++;
        }
        $em->flush();
        return $this->render('server/create.html.twig', [
            'nbrPlanet' => $nbrPlanets,
        ]);
    }

    /**
     * @Route("/destruction-serveur", name="destroy")
     * @Route("/destruction-serveur/", name="destroy_withSlash")
     */
    public function destroyServerAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $allys = $em->getRepository('App:Ally')
            ->createQueryBuilder('al')
            ->getQuery()
            ->getResult();

        foreach ($allys as $ally) {
            $em->remove($ally);
            $em->flush();
        }

        $buildings = $em->getRepository('App:Building')
            ->createQueryBuilder('bu')
            ->getQuery()
            ->getResult();

        foreach ($buildings as $building) {
            $em->remove($building);
            $em->flush();
        }

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('pl')
            ->getQuery()
            ->getResult();

        foreach ($planets as $planet) {
            $em->remove($planet);
            $em->flush();
        }

        $scientists = $em->getRepository('App:Scientist')
            ->createQueryBuilder('scien')
            ->getQuery()
            ->getResult();

        foreach ($scientists as $scientist) {
            $em->remove($scientist);
            $em->flush();
        }

        $soldiers = $em->getRepository('App:Soldier')
            ->createQueryBuilder('sol')
            ->getQuery()
            ->getResult();

        foreach ($soldiers as $soldier) {
            $em->remove($soldier);
            $em->flush();
        }

        $workers = $em->getRepository('App:Worker')
            ->createQueryBuilder('wor')
            ->getQuery()
            ->getResult();

        foreach ($workers as $worker) {
            $em->remove($worker);
            $em->flush();
        }

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('sec')
            ->getQuery()
            ->getResult();

        foreach ($sectors as $sector) {
            $em->remove($sector);
            $em->flush();
        }

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('gal')
            ->getQuery()
            ->getResult();

        foreach ($galaxys as $galaxy) {
            $em->remove($galaxy);
            $em->flush();
        }
        return $this->render('server/destroy.html.twig');
    }
}