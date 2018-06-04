<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;
use Dateinterval;

class DeployController extends Controller
{
    /**
     * @Route("/deployer-radar/{idp}/{fleet}/", name="deploy_radar", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function deployRadarAction($idp, $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 7200 . 'S'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if($fleet->getRadarShip() && $planet->getEmpty() == true) {
            $fleet->setRadarShip($fleet->getRadarShip() - 1);
            if($planet->getSkyRadar()) {
                $planet->setSkyRadar($planet->getSkyRadar() + 1);
            } else {
                $planet->setUser($fleet->getUser());
                $planet->setName('Radar');
                $planet->setSkyRadar(1);
                $planet->setRadarAt($now);
            }
            $em->persist($planet);
            $em->persist($fleet);
            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            }
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition()));
    }
    /**
     * @Route("/deployer-brouilleur/{idp}/{fleet}/", name="deploy_brouilleur", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function deployBrouilleurAction($idp, $fleet)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 3600 . 'S'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if($fleet->getBrouilleurShip() && $planet->getEmpty() == true) {
            $fleet->setBrouilleurShip($fleet->getBrouilleurShip() - 1);
            if($planet->getSkyBrouilleur()) {
                $planet->setSkyBrouilleur($planet->getSkyBrouilleur() + 1);
            } else {
                $planet->setUser($fleet->getUser());
                $planet->setName('Brouilleur');
                $planet->setSkyBrouilleur(1);
                $planet->setBrouilleurAt($now);
            }
            $em->persist($planet);
            $em->persist($fleet);
            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            }
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition()));
    }
    /**
     * @Route("/deployer-lunar/{idp}/{fleet}/", name="deploy_moonMaker", requirements={"idp"="\d+", "fleet"="\d+"})
     */
    public function deployMoonMakerAction($idp, $fleet)
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

        $fleet = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.id = :id')
            ->setParameters(array('id' => $fleet))
            ->getQuery()
            ->getOneOrNullResult();

        $planet = $fleet->getPlanet();
        if($fleet->getMoonMaker() && $planet->getEmpty() == true &&
            $planet->getNbCdr() > 10000000 && $planet->getWtCdr() > 10000000) {
            $fleet->setMoonMaker($fleet->getMoonMaker() - 1);
            $planet->setUser($fleet->getUser());
            $planet->setEmpty(false);
            $planet->setMoon(true);
            $planet->setNbProduction(0);
            $planet->setWtProduction(0);
            $planet->setScientist(0);
            $planet->setName('Lune');
            $image = ['moon1.png', 'moon2.png', 'moon3.png', 'moon4.png', 'moon5.png'];
            $planet->setImageName($image[rand(0, 4)]);
            if ($planet->getNbCdr() > 2000000 && $planet->getWtCdr() > 2000000) {
                if ($planet->getNbCdr() < 5000000 && $planet->getWtCdr() < 5000000) {
                    $planet->setGround(rand(100, 150));
                    $planet->setSky(rand(10, 25));
                } elseif ($planet->getNbCdr() < 10000000 && $planet->getWtCdr() < 10000000) {
                    $planet->setGround(rand(150, 180));
                    $planet->setSky(rand(8, 23));
                } elseif ($planet->getNbCdr() < 20000000 && $planet->getWtCdr() < 20000000) {
                    $planet->setGround(rand(180, 210));
                    $planet->setSky(rand(6, 21));
                } elseif ($planet->getNbCdr() < 50000000 && $planet->getWtCdr() < 50000000) {
                    $planet->setGround(rand(210, 240));
                    $planet->setSky(rand(4, 19));
                } elseif ($planet->getNbCdr() < 75000000 && $planet->getWtCdr() < 75000000) {
                    $planet->setGround(rand(240, 280));
                    $planet->setSky(rand(2, 17));
                } else {
                    $planet->setGround(rand(280, 350));
                    $planet->setSky(rand(15, 50));
                }
            }
            $planet->setNbCdr(0);
            $planet->setWtCdr(0);
            $em->persist($planet);
            $em->persist($fleet);
            if($fleet->getNbrShips() == 0) {
                $em->remove($fleet);
            }
            $em->flush();
        }

        return $this->redirectToRoute('map', array('idp' => $usePlanet->getId(), 'id' => $planet->getSector()->getPosition()));
    }
}