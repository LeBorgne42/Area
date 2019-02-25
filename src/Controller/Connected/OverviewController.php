<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class OverviewController extends AbstractController
{
    /**
     * @Route("/empire/{idp}", name="overview", requirements={"idp"="\d+"})
     */
    public function overviewAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $allPlanets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameters(['user' => $this->getUser()])
            ->orderBy('p.id')
            ->getQuery()
            ->getResult();

        $attackFleets = new \ArrayObject();
        foreach ($allPlanets as $planet) {
            $allFleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.sector', 's')
                ->join('s.galaxy', 'g')
                ->where('f.user != :user')
                ->andWhere('f.planete = :planete')
                ->andWhere('s.position = :sector')
                ->andWhere('g.position = :galaxy')
                ->setParameters(['user' => $user, 'planete' => $planet->getPosition(), 'sector' => $planet->getSector()->getPosition(), 'galaxy' =>$planet->getSector()->getGalaxy()->getPosition()])
                ->orderBy('f.flightTime')
                ->getQuery()
                ->getResult();

            if($allFleets) {
                $attackFleets = $allFleets;
            }
        }
        if (count($attackFleets) == 0) {
            $attackFleets = null;
        }


        $oneHour = new DateTime();
        $oneHour->setTimezone(new DateTimeZone('Europe/Paris'));
        $oneHour->add(new DateInterval('PT' . 3600 . 'S'));
        $fleetMove = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->andWhere('f.flightTime < :time')
            ->setParameters(['user' => $user, 'time' => $oneHour])
            ->orderBy('f.flightTime')
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();

        if (count($fleetMove) == 0) {
            $fleetMove = null;
        }

        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);

        if ($form_image->isSubmitted()) {
            $em->flush();
        }

        return $this->render('connected/overview.html.twig', [
            'form_image' => $form_image->createView(),
            'usePlanet' => $usePlanet,
            'date' => $now,
            'attackFleets' => $attackFleets,
            'fleetMove' => $fleetMove,
        ]);
    }

    /**
     * @Route("/game-over/", name="game_over")
     */
    public function gameOverAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            if($user->getColPlanets() == 0 && $user->getGameOver() == null) {
                $user->setGameOver($user->getUserName());

                $em->flush();
            }
            if($user->getRank()) {
                $user->setBitcoin(25000);
                $user->setAlly(null);
                $user->setSearch(null);
                $user->setRank(null);
                $user->setGrade(null);
                $user->setJoinAllyAt(null);
                $user->setAllyBan(null);
                $user->setScientistProduction(1);
                $user->setSearchAt(null);
                /*$user->setPlasma(0);
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
                $user->setDiscipline(0);*/
                foreach ($user->getSalons() as $salon) {
                    $salon->removeUser($user);
                }

                $salon = $em->getRepository('App:Salon')
                    ->createQueryBuilder('s')
                    ->where('s.name = :name')
                    ->setParameters(['name' => 'Public'])
                    ->getQuery()
                    ->getOneOrNullResult();

                $salon->removeUser($user);
                $user->setSalons(null);

                $em->flush();
            }
            $galaxys = $em->getRepository('App:Galaxy')
                ->createQueryBuilder('g')
                ->orderBy('g.position', 'ASC')
                ->getQuery()
                ->getResult();

            return $this->render('connected/game_over.html.twig', [
                'galaxys' => $galaxys,
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }
}