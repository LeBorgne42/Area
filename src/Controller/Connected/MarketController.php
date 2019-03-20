<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MarketType;
use App\Entity\Planet;
use App\Entity\Report;
use App\Entity\Fleet;
use DateTime;
use DateTimeZone;
use DateInterval;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class MarketController extends AbstractController
{
    /**
     * @Route("/marchands/{idp}", name="market", requirements={"idp"="\d+"})
     */
    public function marketAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $quests = $em->getRepository('App:Quest')
            ->createQueryBuilder('q')
            ->select('q.name, q.gain')
            ->join('q.users', 'u')
            ->where('u.id = :user')
            ->setParameters(['user' => $user->getId()])
            ->getQuery()
            ->getResult();

        $planetsSeller = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.autoSeller = true')
            ->setParameters(['user' => $user])
            ->getQuery()
            ->getResult();

        $planetsNoSell = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.autoSeller = false')
            ->setParameters(['user' => $user])
            ->getQuery()
            ->getResult();

        $form_market = $this->createForm(MarketType::class, null, ["user" => $user->getId()]);
        $form_market->handleRequest($request);

        $form_seller = $this->createForm(MarketType::class, null, ["user" => $user->getId()]);
        $form_seller->handleRequest($request);

        if ($form_market->isSubmitted() && $form_market->isValid()) {
            $planetBuy = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameters(['id' => $form_market->get('planet')->getData()])
                ->getQuery()
                ->getOneOrNullResult();

            if(!$planetBuy) {
                $planetBuy = $usePlanet;
            }
            $cost = (abs($form_market->get('bitcoin')->getData()) / 5) + (abs($form_market->get('soldier')->getData()) * 5) + (abs($form_market->get('worker')->getData()) * 2);
            $cost = ceil($cost);
            if(($cost > $user->getRank()->getWarPoint() ||
                ($planetBuy->getSoldier() + abs($form_market->get('soldier')->getData())) > $planetBuy->getSoldierMax()) ||
                    ($planetBuy->getWorker() + abs($form_market->get('worker')->getData())) > $planetBuy->getWorkerMax()) {
                return $this->redirectToRoute('market', ['idp' => $usePlanet->getId()]);
            }

            $user->setBitcoin($user->getBitcoin() + abs($form_market->get('bitcoin')->getData()));
            $planetBuy->setSoldier($planetBuy->getSoldier() + abs($form_market->get('soldier')->getData()));
            $planetBuy->setWorker($planetBuy->getWorker() + abs($form_market->get('worker')->getData()));
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() - $cost);
            $quest = $user->checkQuests('pdg');
            if($quest) {
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                $user->removeQuest($quest);
            }

            $em->flush();
            return $this->redirectToRoute('market', ['idp' => $usePlanet->getId()]);
        }

        if ($form_seller->isSubmitted() && $form_seller->isValid()) {

        }

        if(($user->getTutorial() == 16)) {
            $user->setTutorial(17);
            $em->flush();
        }

        return $this->render('connected/market.html.twig', [
            'usePlanet' => $usePlanet,
            'form_market' => $form_market->createView(),
            'quests' => $quests,
            'planetsSeller' => $planetsSeller,
            'planetsNoSell' => $planetsNoSell
        ]);
    }

    /**
     * @Route("/ajouter-ajouter-marchand/{idp}//{planet}", name="planet_seller_add", requirements={"idp"="\d+","planet"="\d+"})
     */
    public function planetAddAction($idp, Planet $planet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        if($user == $planet->getUser()) {
            $planet->setAutoSeller(true);
            $em->flush();
        }

        return $this->redirectToRoute('market', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/planete-enlever-marchand/{idp}/{planet}", name="planet_seller_sub", requirements={"idp"="\d+","planet"="\d+"})
     */
    public function planetSubAction($idp, Planet $planet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        if($user == $planet->getUser()) {
            $planet->setAutoSeller(false);
            $em->flush();
        }

        return $this->redirectToRoute('market', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/vente-auto-marchand/{idp}/", name="planets_seller", requirements={"idp"="\d+"})
     */
    public function sellMerchantAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        $merchant = $em->getRepository('App:User')->findOneBy(['merchant' => 1]);
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $planetsSeller = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->andWhere('p.autoSeller = true')
            ->setParameters(['user' => $user])
            ->getQuery()
            ->getResult();

        $gain = 0;
        $newWarPointS = 0;
        foreach ($planetsSeller as $planet) {
            if ($planet->getOffensiveFleet($user) != 'ennemy') {
                if ($user->getAlly() && $user->getAlly()->getPolitic() == 'democrat') {
                    if ($user->getPoliticMerchant() > 0) {
                        $gain = $gain + round((($planet->getWater() * 0.5) + ($planet->getNiobium() * 0.25)) * (1 + ($user->getPoliticMerchant() / 20)));
                    } else {
                        $gain = $gain + round((($planet->getWater() * 0.5) + ($planet->getNiobium() * 0.25)));
                    }
                } else {
                    $gain = $gain + round((($planet->getWater() * 0.5) + ($planet->getNiobium() * 0.25)) * 0.75);
                }
                $newWarPointS = $newWarPointS + round((($planet->getWater() / 3) + ($planet->getNiobium() / 6)) / 1000);
                $planet->setNiobium(0);
                $planet->setWater(0);
                if ($gain != 0) {
                    $repor = new DateTime();
                    $repor->setTimezone(new DateTimeZone('Europe/Paris'));
                    $repor->add(new DateInterval('PT' . 1200 . 'S'));
                    $fleet = new Fleet();
                    $fleet->setMoonMaker(1);
                    $fleet->setUser($merchant);
                    $fleet->setPlanet($planet);
                    $fleet->setPlanete(26);
                    $fleet->setSector($planet->getSector());
                    $fleet->setFlightTime($repor);
                    $fleet->setAttack(0);
                    $fleet->setName('Cargos Marchands');
                    $em->persist($fleet);
                }
            }
        }
        if ($gain != 0) {
            $reportSell = new Report();
            $reportSell->setSendAt($now);
            $reportSell->setUser($user);
            $reportSell->setTitle("Vente aux marchands");
            $reportSell->setImageName("sell_report.jpg");
            $reportSell->setContent("Votre vente aux marchands vous a rapporté " . number_format($gain) . " bitcoin. Et " . number_format($newWarPointS) . " points de Guerre.");
            $em->persist($reportSell);
            $user->setBitcoin($user->getBitcoin() + $gain);
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $newWarPointS);
            $server->setNbrSell($server->getNbrSell() + 1);
            $user->setViewReport(false);
            $quest = $user->checkQuests('sell');
            if($quest) {
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                $user->removeQuest($quest);
            }
        }

        $em->flush();

        return $this->redirectToRoute('market', ['idp' => $usePlanet->getId()]);
    }
}