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
            $planet->setGroundPlace(2);
            $planet->setHunter(5);
            $planet->setNiobium(15000);
            $planet->setWater(10000);
            $planet->setFregate(2);
            $planet->setWorker(25000);
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
        $em->flush();

        return $this->redirectToRoute('login');
    }
}