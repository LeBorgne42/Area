<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
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
     * @Route("/empire/{usePlanet}", name="overview", requirements={"usePlanet"="\d+"})
     */
    public function overviewAction(Request $request, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            return $this->redirectToRoute('game_over');
        }
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

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
            $quest = $user->checkQuests('logo');
            if($quest) {
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                $user->removeQuest($quest);
            }
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
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            if($user->getColPlanets() == 0 && $user->getGameOver() == null) {
                $user->setGameOver($user->getUserName());

                $em->flush();
            }
            if($user->getRank()) {

                foreach ($user->getFleetLists() as $list) {
                    foreach ($list->getFleets() as $fleetL) {
                        $fleetL->setFleetList(null);
                    }
                    $em->remove($list);
                }
                $user->setBitcoin(25000);
                $user->setSearch(null);
                $em->remove($user->getRank(null));
                $user->setRank(null);
                $user->setGrade(null);
                $user->setJoinAllyAt(null);
                $user->setAllyBan(null);
                $user->setScientistProduction(1);
                $user->setSearchAt(null);
                $user->setDemography(0);
                $user->setUtility(0);
                $user->setArmement(0);
                $user->setIndustry(0);
                $user->setTerraformation(round($user->getTerraformation(0) / 2));
                /*$user->setPlasma(0);
                $user->setLaser(0);
                $user->setMissile(0);
                $user->setRecycleur(0);
                $user->setCargo(0);
                $user->setBarge(0);
                $user->setHyperespace(0);
                $user->setDiscipline(0);
                $user->setHeavyShip(0);
                $user->setLightShip(0);
                $user->setOnde(0);
                $user->setHyperespace(0);
                $user->setDiscipline(0);
                $user->setBarbed(0);
                $user->setTank(0);
                $user->setExpansion(0);*/
                $user->setPoliticArmement(0);
                $user->setPoliticCostScientist(0);
                $user->setPoliticArmor(0);
                $user->setPoliticBarge(0);
                $user->setPoliticCargo(0);
                $user->setPoliticColonisation(0);
                $user->setPoliticCostSoldier(0);
                $user->setPoliticCostTank(0);
                $user->setPoliticInvade(0);
                $user->setPoliticMerchant(0);
                $user->setPoliticPdg(0);
                $user->setPoliticProd(0);
                $user->setPoliticRecycleur(0);
                $user->setPoliticSearch(0);
                $user->setPoliticSoldierAtt(0);
                $user->setPoliticSoldierSale(0);
                $user->setPoliticTankDef(0);
                $user->setPoliticWorker(0);
                $user->setPoliticWorkerDef(0);
                $user->setZombieAtt(1);
                if ($user->getAlly()) {
                    $ally = $user->getAlly();
                    if (count($ally->getUsers()) == 1 || ($ally->getPolitic() == 'fascism' && $user->getGrade()->getPlacement() == 1)) {
                        foreach ($ally->getUsers() as $user) {
                        $user->setAlly(null);
                        $user->setGrade(null);
                        $user->setAllyBan($now);
                    }
                        foreach ($ally->getFleets() as $fleet) {
                            $fleet->setAlly(null);
                        }
                        foreach ($ally->getGrades() as $grade) {
                            $em->remove($grade);
                        }
                        foreach ($ally->getSalons() as $salon) {
                            foreach ($salon->getContents() as $content) {
                                $em->remove($content);
                            }
                            $em->remove($salon);
                        }
                        foreach ($ally->getExchanges() as $exchange) {
                            $em->remove($exchange);
                        }

                        foreach ($ally->getPnas() as $pna) {
                            $em->remove($pna);
                        }

                        foreach ($ally->getWars() as $war) {
                            $em->remove($war);
                        }

                        foreach ($ally->getAllieds() as $allied) {
                            $em->remove($allied);
                        }

                        foreach ($ally->getProposals() as $proposal) {
                            $em->remove($proposal);
                        }
                        $em->flush();

                        $pnas = $em->getRepository('App:Pna')
                            ->createQueryBuilder('p')
                            ->where('p.allyTag = :allytag')
                            ->setParameters(['allytag' => $ally->getSigle()])
                            ->getQuery()
                            ->getResult();

                        $pacts = $em->getRepository('App:Allied')
                            ->createQueryBuilder('a')
                            ->where('a.allyTag = :allytag')
                            ->setParameters(['allytag' => $ally->getSigle()])
                            ->getQuery()
                            ->getResult();

                        $wars = $em->getRepository('App:War')
                            ->createQueryBuilder('w')
                            ->where('w.allyTag = :allytag')
                            ->setParameters(['allytag' => $ally->getSigle()])
                            ->getQuery()
                            ->getResult();

                        foreach ($pnas as $pna) {
                            $em->remove($pna);
                        }

                        foreach ($pacts as $pact) {
                            $em->remove($pact);
                        }

                        foreach ($wars as $war) {
                            $em->remove($war);
                        }

                        $ally->setImageName(null);
                        $em->remove($ally);
                    }
                }
                $user->setAlly(null);

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