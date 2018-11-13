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
        $image = [
            'planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png',
            'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png',
            'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png',
            'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png',
            'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png',
            'planet31.png', 'planet32.png', 'planet33.png'
        ];
        $x = 0;
        while($x < 5) {
            $nbrSector = 1;
            $nbrPlanets = 0;
            $galaxy = new Galaxy();
            $galaxy->setPosition(1);
            $em->persist($galaxy);
            /*$salon = new Salon();
            $salon->setName('Public');
            $em->persist($salon);
            $em->flush();*/

            $iaPlayer = $em->getRepository('App:User')->find(['id' => 1]);

            if ($iaPlayer == null) {
                $iaPlayer = new User();
                $now = new DateTime();
                $iaPlayer->setUsername('Les Hydres');
                $iaPlayer->setEmail('support@areauniverse.eu');
                $iaPlayer->setCreatedAt($now);
                $iaPlayer->setPassword(password_hash('ViolGratuit2019', PASSWORD_BCRYPT));
                $iaPlayer->setBitcoin(100);
                $iaPlayer->setImageName('hydre.png');
                $iaPlayer->setTerraformation(999999);
                $rank = new Rank();
                $em->persist($rank);
                $iaPlayer->setRank($rank);
                $em->persist($iaPlayer);

                $iaSalon = $em->getRepository('App:Salon')->find(['id' => 1]);
                $iaSalon->addUser($iaPlayer);
                $em->flush();
            }

            while ($nbrSector <= 100) {
                $nbrPlanet = 1;
                $sector = new Sector();
                $sector->setGalaxy($galaxy);
                $sector->setPosition($nbrSector);
                $em->persist($sector);
                while ($nbrPlanet <= 25) {
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
                        if (rand(1, 20) < 6) {
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
                        } else {
                            $nbrPlanets++;
                            $planet = new Planet();

                            $planet->setImageName($image[rand(0, 32)]);
                            $planet->setSector($sector);
                            $planet->setPosition($nbrPlanet);
                            if (($nbrSector >= 1 && $nbrSector <= 9) || ($nbrSector >= 92 && $nbrSector <= 99) || ($nbrSector % 10 == 0 || $nbrSector % 10 == 1)) {
                                if ($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                                    $planet->setGround(25);
                                    $planet->setSky(5);
                                } else {
                                    $planet->setGround(rand(30, 40));
                                    $planet->setSky(rand(5, 8));
                                }
                            } elseif ($nbrSector == 45 || $nbrSector == 46 || $nbrSector == 55 || $nbrSector == 56) {
                                $planet->setGround(rand(65, 85));
                                $planet->setSky(rand(10, 20));
                            } else {
                                $planet->setGround(rand(48, 60));
                                $planet->setSky(rand(8, 11));
                            }
                        }
                    }
                    $em->persist($planet);
                    $nbrPlanet++;
                }
                $nbrSector++;
            }
            $em->flush();

            $iaPlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.user is null')
                ->andWhere('p.ground > :ground')
                ->andWhere('p.sky > :sky')
                ->andWhere('p.ground < :limitG')
                ->andWhere('p.sky < :limitS')
                ->setParameters(['ground' => 70, 'sky' => 15, 'limitG' => 86, 'limitS' => 21])
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();

            $iaPlayer = $em->getRepository('App:User')->find(['id' => 1]);

            $iaPlanet->setUser($iaPlayer);
            $iaPlanet->setWorker(500000);
            $iaPlanet->setWorkerMax(500000);
            $iaPlanet->setSoldier(150000);
            $iaPlanet->setSoldierMax(150000);
            $iaPlanet->setCaserne(500);
            $iaPlanet->setGround(1300);
            $iaPlanet->setSky(180);
            $iaPlanet->setImageName('hydra_planet.png');
            $iaPlanet->setName('Fort Hydra');
            $iaPlayer->addPlanet($iaPlanet);

            $putFleets = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.sector', 's')
                ->andWhere('s.position in (:pos)')
                ->setParameters(['pos' => [45, 46, 55, 56]])
                ->getQuery()
                ->getResult();

            foreach ($putFleets as $putFleet) {
                $fleet = new Fleet();
                $fleet->setHunterWar(750);
                $fleet->setCorvetWar(125);
                $fleet->setFregatePlasma(50);
                $fleet->setDestroyer(10);
                $fleet->setUser($iaPlayer);
                $fleet->setPlanet($putFleet);
                $fleet->setAttack(1);
                $fleet->setName('Hydra Force');
                $em->persist($fleet);
            }
            $em->flush();
            $x = $x + 1;
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
        $image = [
            'planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png',
            'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png',
            'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png',
            'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png',
            'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png',
            'planet31.png', 'planet32.png', 'planet33.png'
        ];
        $x = 1;
        while($x < 6) {
            $nbrSector = 1;
            $nbrPlanets = 0;
            $galaxy = new Galaxy();
            $galaxy->setPosition($x);
            $em->persist($galaxy);

            while ($nbrSector <= 16) {
                $nbrPlanet = 1;
                $sector = new Sector();
                $sector->setGalaxy($galaxy);
                $sector->setPosition($nbrSector);
                $em->persist($sector);
                while ($nbrPlanet <= 25) {
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
                        } else {
                            $nbrPlanets++;
                            $planet = new Planet();

                            $planet->setImageName($image[rand(0, 32)]);
                            $planet->setSector($sector);
                            $planet->setPosition($nbrPlanet);
                            if (($nbrSector >= 1 && $nbrSector <= 5) || ($nbrSector >= 12 && $nbrSector <= 16) || ($nbrSector == 8 || $nbrSector == 9)) {
                                if ($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                                    $planet->setGround(25);
                                    $planet->setSky(5);
                                } else {
                                    $planet->setGround(rand(30, 40));
                                    $planet->setSky(rand(5, 8));
                                }
                            } elseif ($nbrSector == 6 || $nbrSector == 7 || $nbrSector == 10 || $nbrSector == 11) {
                                $planet->setGround(rand(65, 85));
                                $planet->setSky(rand(10, 20));
                            } else {
                                $planet->setGround(rand(48, 60));
                                $planet->setSky(rand(8, 11));
                            }
                        }
                    }
                    $em->persist($planet);
                    $nbrPlanet++;
                }
                $nbrSector++;
            }
            $em->flush();
            $x = $x + 1;
        }

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

        $users = $em->getRepository('App:User')->findAll();

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameters(['name' => 'Public'])
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
        }

        $sContents = $em->getRepository('App:S_Content')->findAll();

        foreach ($sContents as $sContent) {
            $em->remove($sContent);
        }

        $salons = $em->getRepository('App:Salon')->find(['id' => 1]);

        foreach ($salons as $salon) {
            $em->remove($salon);
        }

        $reports = $em->getRepository('App:Report')->findAll();

        foreach ($reports as $report) {
            $em->remove($report);
        }

        $messages = $em->getRepository('App:Message')->findAll();

        foreach ($messages as $message) {
            $em->remove($message);
        }

        $ranks = $em->getRepository('App:Rank')->findAll();

        foreach ($ranks as $rank) {
            $em->remove($rank);
        }

        $exchanges = $em->getRepository('App:Exchange')->findAll();

        foreach ($exchanges as $exchange) {
            $em->remove($exchange);
        }

        $grades = $em->getRepository('App:Grade')->findAll();

        foreach ($grades as $grade) {
            $em->remove($grade);
        }

        $products = $em->getRepository('App:Product')->findAll();

        foreach ($products as $product) {
            $em->remove($product);
        }

        $fleets = $em->getRepository('App:Fleet')->findAll();

        foreach ($fleets as $fleet) {
            $em->remove($fleet);
        }

        $allys = $em->getRepository('App:Ally')->findAll();

        foreach ($allys as $ally) {
            $em->remove($ally);
        }

        $planets = $em->getRepository('App:Planet')->findAll();

        foreach ($planets as $planet) {
            $em->remove($planet);
        }

        $sectors = $em->getRepository('App:Sector')->findAll();

        foreach ($sectors as $sector) {
            $em->remove($sector);
        }

        $galaxys = $em->getRepository('App:Galaxy')->findAll();

        foreach ($galaxys as $galaxy) {
            $em->remove($galaxy);
        }

        $em->flush();

        return $this->render('server/destroy.html.twig');
    }

    /**
     * @Route("/detruire", name="destroy_sectors")
     * @Route("/detruire/", name="destroy_sectors_withSlash")
     */
    public function destroySectorsAction()
    {
        $em = $this->getDoctrine()->getManager();

        $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->where('s.destroy = true')
            ->getQuery()
            ->getResult();

        foreach ($sectors as $sector) {
            foreach ($sector->getPlanets() as $planet) {
                foreach ($planet->getFleets() as $fleet) {
                    $fleet->setPlanet(null);
                }
                $em->remove($planet);
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
        }

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.planets', 'p')
            ->where('p.id is null')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $user->setGameOver(1);
        }
        $em->flush();

        exit;
    }

    /**
     * @Route("/activer", name="active_server")
     * @Route("/activer/", name="active_server_withSlash")
     */
    public function activeServerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);

        $server->setOpen(1);

        $em->flush();

        exit;
    }

    /**
     * @Route("/desactiver", name="deactive_server")
     * @Route("/desactiver/", name="deactive_server_withSlash")
     */
    public function deactivateServerAction()
    {
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);

        $server->setOpen(0);

        $em->flush();

        exit;
    }
}