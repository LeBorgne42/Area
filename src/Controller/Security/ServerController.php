<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use App\Entity\Sector;
use App\Entity\Galaxy;

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
        $image = ['planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png', 'planet6.png', 'planet7.png', 'planet8.png', 'planet9.png', 'planet10.png', 'planet11.png', 'planet12.png', 'planet13.png', 'planet14.png', 'planet15.png', 'planet16.png', 'planet17.png', 'planet18.png', 'planet19.png', 'planet20.png', 'planet21.png', 'planet22.png', 'planet23.png', 'planet24.png', 'planet25.png', 'planet26.png', 'planet27.png', 'planet28.png', 'planet29.png', 'planet30.png', 'planet31.png', 'planet32.png', 'planet33.png'];
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
                if (($nbrSector == 34 || $nbrSector == 37 || $nbrSector == 64 || $nbrSector == 67) && $nbrPlanet == 13) {
                    $planet = new Planet();
                    $planet->setMerchant(true);
                    $planet->setGround(260);
                    $planet->setSky(55);
                    $planet->setImageName('merchant.png');
                    $planet->setName('Marchands');
                    $planet->setSector($sector);
                    $planet->setPosition($nbrPlanet);
                } else {
                    if (rand(1, 20) < 12) {
                        $planet = new Planet();
                        $planet->setEmpty(true);
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    } elseif (rand(0, 101) < 2) {
                        $planet = new Planet();
                        $planet->setCdr(true);
                        $planet->setImageName('cdr.png');
                        $planet->setName('Astéroïdes');
                        $planet->setNbCdr(9999999999);
                        $planet->setWtCdr(9999999999);
                        $planet->setSector($sector);
                        $planet->setPosition($nbrPlanet);
                    }
                    else {
                        $nbrPlanets++;
                        $planet = new Planet();

                        $planet->setImageName($image[rand(0, 32)]);
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

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->getQuery()
            ->getResult();

        foreach ($users as $user) {
            $user->setBitcoin(50000);
            $user->setAlly(null);
            $user->setScientistProduction(1);
            $user->setSearch(null);
            $user->setGrade(null);
            $user->setJoinAllyAt(null);
            foreach ($user->getProposals() as $proposal) {
                $user->removeProposal($proposal);
            }
            foreach ($user->getFleets() as $fleet) {
                $user->removeFleet($fleet);
            }
            foreach ($user->getPlanets() as $planet) {
                $user->removePlanet($planet);
            }
            $em->persist($user);
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
}