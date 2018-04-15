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
        $nbrSector = 0;
        $position = 0;
        $nbrPlanets = 0;
        $image = ['planet1.png', 'planet2.png', 'planet3.png', 'planet4.png', 'planet5.png'];
        $galaxy = new Galaxy();
        $galaxy->setPosition(1);
        $em->persist($galaxy);

        while($nbrSector <= 99) {
            $nbrPlanet = 0;
            $sector = new Sector();
            $sector->setGalaxy($galaxy);
            $sector->setPosition($nbrSector);
            $em->persist($sector);
            while($nbrPlanet <= 24) {
                $nbrPlanets++;
                $planet = new Planet();
                $planet->setName('empty');
                $planet->setImageName($image[rand(0, 4)]);
                $planet->setSector($sector);
                $planet->setPosition($position);
                if(($nbrSector > 2 && $nbrSector < 9) || ($nbrSector > 92 && $nbrSector < 99) || ($nbrSector % 10 == 0 && $nbrSector % 10 == 1)) {
                    if($nbrPlanet == 4 || $nbrPlanet == 6 || $nbrPlanet == 15 || $nbrPlanet == 17 || $nbrPlanet == 25) {
                        $planet->setLand(60);
                        $planet->setSky(10);
                    } else {
                        $planet->setLand(rand(75, 95));
                        $planet->setSky(rand(4, 15));
                    }
                } elseif($nbrSector == 55 || $nbrSector == 56 || $nbrSector == 65 || $nbrSector == 66) {
                    $planet->setLand(rand(120, 160));
                    $planet->setSky(rand(3, 20));
                } else {
                    $planet->setLand(rand(85, 125));
                    $planet->setSky(rand(6, 30));
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
        return $this->render('server/destroy.html.twig');
    }
}