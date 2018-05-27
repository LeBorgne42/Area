<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MarketType;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MarketController extends Controller
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_market = $this->createForm(MarketType::class, null, array("user" => $user->getId()));
        $form_market->handleRequest($request);

        if ($form_market->isSubmitted() && $form_market->isValid()) {
            $planetBuy = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.id = :id')
                ->setParameters(array('id' => $form_market->get('planet')->getData()))
                ->getQuery()
                ->getOneOrNullResult();

            if(!$planetBuy) {
                $planetBuy = $usePlanet;
            }
            $cost = ($form_market->get('bitcoin')->getData() / 5) + ($form_market->get('soldier')->getData() * 5) + ($form_market->get('worker')->getData() * 2);
            if(($cost > $user->getRank()->getWarPoint() ||
                ($planetBuy->getSoldier() + $form_market->get('soldier')->getData()) > $planetBuy->getSoldierMax()) ||
                    ($planetBuy->getWorker() + $form_market->get('worker')->getData()) > $planetBuy->getWorkerMax()) {
                return $this->redirectToRoute('market', array('idp' => $usePlanet->getId()));
            }

            $user->setBitcoin($user->getBitcoin() + $form_market->get('bitcoin')->getData());
            $planetBuy->setSoldier($planetBuy->getSoldier() + $form_market->get('soldier')->getData());
            $planetBuy->setWorker($planetBuy->getWorker() + $form_market->get('worker')->getData());
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() - $cost);
            $em->persist($planetBuy);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('market', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/market.html.twig', [
            'usePlanet' => $usePlanet,
            'form_market' => $form_market->createView(),
        ]);
    }
}