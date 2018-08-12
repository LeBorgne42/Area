<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use App\Entity\Sector;
use App\Entity\Galaxy;
use App\Entity\Salon;
use App\Entity\User;
use App\Entity\Rank;
use App\Entity\Fleet;
use DateTime;

/**
 * @Route("/serveur")
 * @Security("has_role('ROLE_ADMIN')")
 */
class ServerController extends Controller
{
    /**
     * @Route("/creation-serveur-final", name="create")
     * @Route("/creation-serveur-final/", name="create_withSlash")
     */
    public function createServerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nbrSector = 1;
        $nbrPlanets = 0;
        $image = ['planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png', 'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png', 'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png', 'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png', 'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png', 'planet31.png', 'planet32.png', 'planet33.png'];
        $galaxy = new Galaxy();
        $galaxy->setPosition(2);
        $em->persist($galaxy);
        /*$salon = new Salon();
        $salon->setName('Public');
        $em->persist($salon);
        $em->flush();*/

        $fossoyeurs = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameters(array('id' => 1))
            ->getQuery()
            ->getOneOrNullResult();

        if($fossoyeurs == null) {
            $fossoyeurs = new User();
            $now = new DateTime();
            $fossoyeurs->setUsername('Les hydres');
            $fossoyeurs->setEmail('support@areauniverse.eu');
            $fossoyeurs->setCreatedAt($now);
            $fossoyeurs->setPassword(password_hash('ViolGratuit2018', PASSWORD_BCRYPT));
            $fossoyeurs->setBitcoin(100);
            $fossoyeurs->setImageName('hydre.png');
            $fossoyeurs->setTerraformation(10000);
            $rank = new Rank();
            $em->persist($rank);
            $fossoyeurs->setRank($rank);
            $em->persist($fossoyeurs);
            $em->flush();
        }

        $fosSalon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->getQuery()
            ->getOneOrNullResult();

        $fosSalon->addUser($fossoyeurs);
        $em->persist($fosSalon);


        while($nbrSector <= 100) {
            $nbrPlanet = 1;
            $sector = new Sector();
            $sector->setGalaxy($galaxy);
            $sector->setPosition($nbrSector);
            $em->persist($sector);
            while($nbrPlanet <= 25) {
                if (($nbrSector == 23 || $nbrSector == 28 || $nbrSector == 73 || $nbrSector == 78) && $nbrPlanet == 13) {
                    $planet = new Planet();
                    $planet->setMerchant(true);
                    $planet->setGround(400);
                    $planet->setSky(80);
                    $planet->setImageName('merchant.png');
                    $planet->setName('Marchands');
                    $planet->setSector($sector);
                    $planet->setPosition($nbrPlanet);
                } else {
                    if (rand(1, 20) < 10) {
                        $planet = new Planet();
                        $planet->setEmpty(true);
                        $planet->setName('Vide');
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    } elseif (rand(0, 101) < 2) {
                        $planet = new Planet();
                        $planet->setCdr(true);
                        $planet->setImageName('cdr.png');
                        $planet->setName('Astéroïdes');
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    }
                    else {
                        $nbrPlanets++;
                        $planet = new Planet();

                        $planet->setImageName($image[rand(0, 32)]);
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                        if (($nbrSector >= 1 && $nbrSector <= 9) || ($nbrSector >= 92 && $nbrSector <= 99) || ($nbrSector % 10 == 0 || $nbrSector % 10 == 1)) {
                            if ($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                                $planet->setGround(60);
                                $planet->setSky(10);
                            } else {
                                $planet->setGround(rand(95, 130));
                                $planet->setSky(rand(7, 21));
                            }
                        } elseif ($nbrSector == 45 || $nbrSector == 46 || $nbrSector == 55 || $nbrSector == 56) {
                            $planet->setGround(rand(180, 240));
                            $planet->setSky(rand(3, 25));
                        } else {
                            $planet->setGround(rand(130, 180));
                            $planet->setSky(rand(15, 30));
                        }
                    }
                }
                $em->persist($planet);
                $nbrPlanet++;
            }
            $nbrSector++;
        }
        $em->flush();

        $fosPlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user is null')
            ->andWhere('p.ground > :ground')
            ->andWhere('p.sky > :sky')
            ->andWhere('p.ground < :limitG')
            ->andWhere('p.sky < :limitS')
            ->andWhere('p.empty = :false')
            ->andWhere('p.cdr = :false')
            ->setParameters(array('ground' => 180, 'sky' => 15, 'limitG' => 240, 'limitS' => 25, 'false' => false))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $fosPlanet->setUser($fossoyeurs);
        $fosPlanet->setWorker(500000);
        $fosPlanet->setWorkerMax(500000);
        $fosPlanet->setSoldier(150000);
        $fosPlanet->setSoldierMax(150000);
        $fosPlanet->setCaserne(500);
        $fosPlanet->setGround(1300);
        $fosPlanet->setSky(180);
        $fosPlanet->setName('Fort Hydra');
        $fossoyeurs->addPlanet($fosPlanet);
        $em->persist($fosPlanet);

        $em->flush();

        $putFleets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->join('p.sector', 's')
            ->andWhere('p.empty = :false')
            ->andWhere('p.cdr = :false')
            ->andWhere('s.position in (:pos)')
            ->setParameters(array('pos' => [45, 46, 55, 56], 'false' => false))
            ->getQuery()
            ->getResult();

        foreach($putFleets as $putFleet) {
            $fleet = new Fleet();
            $fleet->setHunterWar(750);
            $fleet->setCorvetWar(125);
            $fleet->setFregatePlasma(50);
            $fleet->setDestroyer(10);
            $fleet->setUser($fossoyeurs);
            $fleet->setPlanet($putFleet);
            $fleet->setAttack(1);
            $fleet->setName('Hydra Force');
            $em->persist($fleet);
            $em->flush();
        }

        return $this->render('server/create.html.twig', [
            'nbrPlanet' => $nbrPlanets,
        ]);
    }

    /**
     * @Route("/creation-serveur-petit", name="create_little")
     * @Route("/creation-serveur-petit/", name="create_little_withSlash")
     */
    public function createServerLittleAction()
    {
        $em = $this->getDoctrine()->getManager();
        $nbrSector = 1;
        $nbrPlanets = 0;
        $image = ['planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png', 'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png', 'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png', 'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png', 'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png', 'planet31.png', 'planet32.png', 'planet33.png'];
        $galaxy = new Galaxy();
        $galaxy->setPosition(1);
        $em->persist($galaxy);

        while($nbrSector <= 16) {
            $nbrPlanet = 1;
            $sector = new Sector();
            $sector->setGalaxy($galaxy);
            $sector->setPosition($nbrSector);
            $em->persist($sector);
            while($nbrPlanet <= 25) {
                if (($nbrSector == 7 || $nbrSector == 10) && $nbrPlanet == 13) {
                    $planet = new Planet();
                    $planet->setMerchant(true);
                    $planet->setGround(400);
                    $planet->setSky(80);
                    $planet->setImageName('merchant.png');
                    $planet->setName('Marchands');
                    $planet->setSector($sector);
                    $planet->setPosition($nbrPlanet);
                } else {
                    if (rand(1, 20) < 2) {
                        $planet = new Planet();
                        $planet->setEmpty(true);
                        $planet->setName('Vide');
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    } elseif (rand(0, 101) < 1) {
                        $planet = new Planet();
                        $planet->setCdr(true);
                        $planet->setImageName('cdr.png');
                        $planet->setName('Astéroïdes');
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    }
                    else {
                        $nbrPlanets++;
                        $planet = new Planet();

                        $planet->setImageName($image[rand(0, 32)]);
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                        if (($nbrSector >= 1 && $nbrSector <= 5) || ($nbrSector >= 12 && $nbrSector <= 16) || ($nbrSector == 8 || $nbrSector == 9)) {
                            if ($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                                $planet->setGround(60);
                                $planet->setSky(10);
                            } else {
                                $planet->setGround(rand(95, 130));
                                $planet->setSky(rand(7, 21));
                            }
                        } elseif ($nbrSector == 6 || $nbrSector == 7 || $nbrSector == 10 || $nbrSector == 11) {
                            $planet->setGround(rand(180, 240));
                            $planet->setSky(rand(3, 25));
                        } else {
                            $planet->setGround(rand(130, 180));
                            $planet->setSky(rand(15, 30));
                        }
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
    public function destroyServerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameters(array('name' => 'Public'))
            ->getQuery()
            ->getOneOrNullResult();

        foreach ($users as $user) {
            $user->setBitcoin(25000);
            $user->setAlly(null);
            $user->setSearch(null);
            $user->setRank(null);
            $user->setGrade(null);
            $user->setJoinAllyAt(null);
            $user->setAllyBan(null);
            $user->setScientistProduction(1);
            $user->setSearchAt(null);
            $user->setPlasma(0);
            $user->setLaser(0);
            $user->setMissile(0);
            $user->setArmement(0);
            $user->setRecycleur(0);
            $user->setCargo(0);
            $user->setTerraformation(0);
            $user->setDemography(0);
            $user->setUtility(0);
            $user->setBarge(0);
            $user->setHyperespace(0);
            $user->setDiscipline(0);
            $user->setHeavyShip(0);
            $user->setLightShip(0);
            $user->setIndustry(0);
            $user->setOnde(0);
            $user->setHyperespace(0);
            $user->setDiscipline(0);
            foreach ($user->getProposals() as $proposal) {
                $user->removeProposal($proposal);
            }
            foreach ($user->getFleets() as $fleet) {
                $user->removeFleet($fleet);
            }
            foreach ($user->getPlanets() as $planet) {
                $user->removePlanet($planet);
            }
            $salon->removeUser($user);
            $em->persist($user);
            $em->flush();
        }

        $sContents = $em->getRepository('App:S_Content')
            ->createQueryBuilder('sc')
            ->getQuery()
            ->getResult();

        foreach ($sContents as $sContent) {
            $em->remove($sContent);
            $em->flush();
        }

        $salons = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.id != :id')
            ->setParameters(array('id' => 1))
            ->getQuery()
            ->getResult();

        foreach ($salons as $salon) {
            $em->remove($salon);
            $em->flush();
        }

        $reports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->getQuery()
            ->getResult();

        foreach ($reports as $report) {
            $em->remove($report);
            $em->flush();
        }

        $messages = $em->getRepository('App:Message')
            ->createQueryBuilder('m')
            ->getQuery()
            ->getResult();

        foreach ($messages as $message) {
            $em->remove($message);
            $em->flush();
        }

        $ranks = $em->getRepository('App:Rank')
            ->createQueryBuilder('r')
            ->getQuery()
            ->getResult();

        foreach ($ranks as $rank) {
            $em->remove($rank);
            $em->flush();
        }

        $exchanges = $em->getRepository('App:Exchange')
            ->createQueryBuilder('ex')
            ->getQuery()
            ->getResult();

        foreach ($exchanges as $exchange) {
            $em->remove($exchange);
            $em->flush();
        }

        $grades = $em->getRepository('App:Grade')
            ->createQueryBuilder('g')
            ->getQuery()
            ->getResult();

        foreach ($grades as $grade) {
            $em->remove($grade);
            $em->flush();
        }

        $products = $em->getRepository('App:Product')
            ->createQueryBuilder('pr')
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $em->remove($product);
            $em->flush();
        }

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('fl')
            ->getQuery()
            ->getResult();

        foreach ($fleets as $fleet) {
            $em->remove($fleet);
            $em->flush();
        }

        $allys = $em->getRepository('App:Ally')
            ->createQueryBuilder('al')
            ->getQuery()
            ->getResult();

        foreach ($allys as $ally) {
            $em->remove($ally);
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



    /**
     * @Route("/destroy-sectors", name="destroy_sectors")
     * @Route("/destroy-sectors/", name="destroy_sectors_withSlash")
     */
    public function destroySectorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->where('s.destroy = :true')
            ->setParameters(array('true' => 1))
            ->getQuery()
            ->getResult();

        foreach ($sectors as $sector) {
            foreach ($sector->getPlanets() as $planet) {
                foreach ($planet->getFleets() as $fleet) {
                    $fleet->setPlanet(null);
                    $em->persist($fleet);
                }
                $em->remove($planet);
                $em->flush();
            }
        }

        $fleets = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.planet is null')
            ->andWhere('f.flightTime is null')
            ->getQuery()
            ->getResult();

        foreach ($fleets as $fleet) {
            $em->remove($fleet);
            $em->flush();
        }

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.planets', 'p')
            ->where('p.id is null')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $user->setGameOver(1);
            $em->flush();
        }

        exit;
    }
}