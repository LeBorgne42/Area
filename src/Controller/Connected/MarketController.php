<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MarketType;
use DateTime;
use Dateinterval;
use DateTimeZone;

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
            ->join('q.users', 'u')
            ->where('u.id = :user')
            ->setParameters(['user' => $user->getId()])
            ->getQuery()
            ->getResult();

        $form_market = $this->createForm(MarketType::class, null, ["user" => $user->getId()]);
        $form_market->handleRequest($request);

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

        return $this->render('connected/market.html.twig', [
            'usePlanet' => $usePlanet,
            'form_market' => $form_market->createView(),
            'quests' => $quests
        ]);
    }
}