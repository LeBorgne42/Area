<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\CaserneRecruitType;
use App\Form\Front\ScientistRecruitType;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SoldierController extends Controller
{
    /**
     * @Route("/entrainement/{idp}", name="soldier", requirements={"idp"="\d+"})
     */
    public function soldierAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $form_caserneRecruit = $this->createForm(CaserneRecruitType::class);
        $form_caserneRecruit->handleRequest($request);

        $form_scientistRecruit = $this->createForm(ScientistRecruitType::class);
        $form_scientistRecruit->handleRequest($request);

        if ($form_caserneRecruit->isSubmitted() && $form_caserneRecruit->isValid()) {
            $nbrSoldier = abs($form_caserneRecruit->get('soldier')->getData());
            if($nbrSoldier > ($user->getBitcoin() / 75) ||
                ($nbrSoldier > ($usePlanet->getWorker() / 3) || ($usePlanet->getSoldier() + $nbrSoldier) > $usePlanet->getSoldierMax()) ||
                ($usePlanet->getWorker() < 5000)) {
                return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
            }
            if($usePlanet->getSoldierAt()) {
                if ($usePlanet->getSoldierAtNbr() + $nbrSoldier > $usePlanet->getSoldierMax()) {
                    return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                }
                $tmpSoldier = $usePlanet->getSoldierAtNbr() - $usePlanet->getSoldier();
                $now->add(new DateInterval('PT' . ((($nbrSoldier + $tmpSoldier))) . 'S'));
                $usePlanet->setSoldierAtNbr($usePlanet->getSoldierAtNbr() + $nbrSoldier);
            } else {
                $now->add(new DateInterval('PT' . $nbrSoldier . 'S'));
                $usePlanet->setSoldierAtNbr($nbrSoldier);
            }
            $usePlanet->setWorker($usePlanet->getWorker() - ($nbrSoldier * 3));
            $usePlanet->setSoldierAt($now);
            $user->setBitcoin($user->getBitcoin() - ($nbrSoldier * 75));

            $em->flush();
            return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
        }

        if ($form_scientistRecruit->isSubmitted() && $form_scientistRecruit->isValid()) {
            $nbrScientist = abs($form_scientistRecruit->get('scientist')->getData());
            if($nbrScientist > ($user->getBitcoin() / 250) ||
                $nbrScientist > ($usePlanet->getWorker() / 10) ||
                ($usePlanet->getWorker() < 5000 || ($usePlanet->getScientist() + $nbrScientist) > $usePlanet->getScientistMax())) {
                return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
            }
            if($usePlanet->getScientistAt()) {
                if ($usePlanet->getScientistAtNbr() + $nbrScientist > $usePlanet->getScientistMax()) {
                    return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                }
                $tmpScientist = $usePlanet->getScientistAtNbr() - $usePlanet->getScientist();
                $now->add(new DateInterval('PT' . round((($nbrScientist + $tmpScientist) * 60)/ $user->getScientistProduction()) . 'S'));
                $usePlanet->setScientistAtNbr($usePlanet->getScientistAtNbr() + $nbrScientist);
            } else {
                $now->add(new DateInterval('PT' . round(($nbrScientist * 60)/ $user->getScientistProduction()) . 'S'));
                $usePlanet->setScientistAtNbr($nbrScientist);
            }
            $usePlanet->setWorker($usePlanet->getWorker() - ($nbrScientist * 10));
            $user->setBitcoin($user->getBitcoin() - ($nbrScientist * 250));
            $usePlanet->setScientistAt($now);

            $em->flush();
            return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/soldier.html.twig', [
            'usePlanet' => $usePlanet,
            'form_caserneRecruit' => $form_caserneRecruit->createView(),
            'form_scientistRecruit' => $form_scientistRecruit->createView(),
        ]);
    }
}