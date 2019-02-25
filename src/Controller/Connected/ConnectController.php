<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Rank;
use DateTime;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ConnectController extends AbstractController
{
    /**
     * @Route("/connection/{id}", name="connect_server", requirements={"id"="\d+"})
     */
    public function connectServerAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByFirstPlanet($user->getUsername());

        if($usePlanet) {
            return $this->redirectToRoute('overview', ['idp' => $usePlanet->getId(), 'usePlanet' => $usePlanet]);
        }

        $planet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->join('p.sector', 's')
            ->join('s.galaxy', 'g')
            ->where('p.user is null')
            ->andWhere('p.ground = :ground')
            ->andWhere('p.sky = :sky')
            ->andWhere('g.position = :id')
            ->setParameters(['ground' => 25, 'sky' => 5, 'id' => $id])
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if($planet) {
            $planet->setUser($user);
            $planet->setName('Nova Terra');
            $planet->setSonde(10);
            $planet->setRadar(1);
            $planet->setGroundPlace(10);
            $planet->setSkyPlace(1);
            $planet->setMiner(3);
            $planet->setNbProduction(12.6);
            $planet->setWtProduction(11.54);
            $planet->setExtractor(3);
            $planet->setSpaceShip(1);
            $planet->setHunter(5);
            $planet->setNiobium(15000);
            $planet->setWater(10000);
            $planet->setFregate(2);
            $planet->setWorker(50000);
            $planet->setSoldier(1000);
            $planet->setColonizer(1);
            $user->addPlanet($planet);
        } else {
            $this->addFlash("full", "Cette galaxie est déjà pleine !");
            $galaxys = $em->getRepository('App:Galaxy')
                ->createQueryBuilder('g')
                ->orderBy('g.position', 'ASC')
                ->getQuery()
                ->getResult();

            return $this->render('connected/play.html.twig', [
                'galaxys' => $galaxys
            ]);
        }

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameters(['name' => 'Public'])
            ->getQuery()
            ->getOneOrNullResult();

        $rank = new Rank();
        $em->persist($rank);
        $user->setRank($rank);
        $user->setTutorial(1);
        $user->setGameOver(null);
        $salon->addUser($user);
        $questOne = $em->getRepository('App:Quest')->findOneById(2);
        $questTwo = $em->getRepository('App:Quest')->findOneById(4);
        $questTree = $em->getRepository('App:Quest')->findOneById(50);
        $user->addQuest($questOne);
        $user->addQuest($questTwo);
        $user->addQuest($questTree);
        $em->flush();

        return $this->redirectToRoute('login');
    }
}