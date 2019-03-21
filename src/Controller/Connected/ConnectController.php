<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Rank;
use App\Entity\Report;
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
            $planet->setSoldier(200);
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
        $user->setDailyConnect($now);
        $user->setGameOver(null);
        $salon->addUser($user);
        foreach ($user->getQuests() as $quest) {
            $user->removeQuest($quest);
        }
        $questOne = $em->getRepository('App:Quest')->findOneById(2);
        $questTwo = $em->getRepository('App:Quest')->findOneById(4);
        $questTree = $em->getRepository('App:Quest')->findOneById(50);
        $user->addQuest($questOne);
        $user->addQuest($questTwo);
        $user->addQuest($questTree);

        $report = new Report();
        $report->setSendAt($now);
        $report->setUser($user);
        $report->setTitle("Bienvenu parmis nous "  . $user->getUsername() . " !");
        $report->setImageName("welcome_report.jpg");
        $report->setContent("Une épidémie s'est déclaré sur la Terre et en ce moment même il est fort a parier qu'elle est aux mains des hordes zombies. Vous et quelques autres commandant de vaisseaux spatiaux avez eu la chance de fuir avec un certains nombre de travailleurs/soldats. Remontez notre civilisation et préparez vous, les Zombies ne sont pas arrivés par hasard sur Terre... Bon courage commandant. (Pour recevoir de l'aide : La page Salon ou rendez-vous sur le discord)");
        $em->persist($report);
        $user->setViewReport(false);

        $em->flush();

        return $this->redirectToRoute('login');
    }
}