<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\CaserneRecruitType;
use App\Form\Front\ScientistRecruitType;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class SoldierController extends AbstractController
{
    /**
     * @Route("/entrainement/{idp}", name="soldier", requirements={"idp"="\d+"})
     */
    public function soldierAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

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

        if ($form_caserneRecruit->isSubmitted() && $form_caserneRecruit->isValid()) {
            if ($form_caserneRecruit->get('soldier')->getData() && $usePlanet->getCaserne() > 0) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrSoldier = abs($form_caserneRecruit->get('soldier')->getData());
                if($nbrSoldier * 75 > $user->getBitcoin() ||
                    ($nbrSoldier * 2 > $usePlanet->getWorker() || ($usePlanet->getSoldier() + $nbrSoldier) > $usePlanet->getSoldierMax()) ||
                    ($usePlanet->getWorker() < 5000)) {
                    return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                }
                if($usePlanet->getSoldierAt()) {
                    if ($usePlanet->getSoldier() + $usePlanet->getSoldierAtNbr() + $nbrSoldier > $usePlanet->getSoldierMax()) {
                        return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                    }
                    $tmpSoldier = $usePlanet->getSoldierAtNbr();
                    $now->add(new DateInterval('PT' . round(($nbrSoldier + $tmpSoldier) / 10) . 'S'));  // X10 NORMAL GAME
                    $usePlanet->setSoldierAtNbr($tmpSoldier + $nbrSoldier);
                } else {
                    $now->add(new DateInterval('PT' . round($nbrSoldier / 10) . 'S')); // X10 NORMAL GAME
                    $usePlanet->setSoldierAtNbr($nbrSoldier);
                }
                $usePlanet->setWorker($usePlanet->getWorker() - ($nbrSoldier * 2));
                $usePlanet->setSoldierAt($now);
                $user->setBitcoin($user->getBitcoin() - ($nbrSoldier * 75));
                $quest = $user->checkQuests('soldier');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            if ($form_caserneRecruit->get('tank')->getData() && $usePlanet->getBunker() > 0 && $usePlanet->getLightUsine() > 0 && $user->getTank() == 1) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrTank = abs($form_caserneRecruit->get('tank')->getData());
                if($nbrTank * 600 > $user->getBitcoin() ||
                    $nbrTank * 5 > $usePlanet->getWorker() ||
                    $nbrTank * 40000 > $usePlanet->getNiobium() ||
                    ($usePlanet->getWorker() < 5000 || ($usePlanet->getTank() + $nbrTank) > 500)) {
                    return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                }
                if($usePlanet->getTankAt()) {
                    if ($usePlanet->getTank() + $usePlanet->getTankAtNbr() + $nbrTank > 500) {
                        return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                    }
                    $tmpTank = $usePlanet->getTankAtNbr();
                    $now->add(new DateInterval('PT' . round(($nbrTank + $tmpTank) * 900) . 'S'));
                    $usePlanet->setTankAtNbr($usePlanet->getTankAtNbr() + $nbrTank);
                } else {
                    $now->add(new DateInterval('PT' . round($nbrTank * 900) . 'S'));
                    $usePlanet->setTankAtNbr($nbrTank);
                }
                $usePlanet->setNiobium($usePlanet->getNiobium() - ($nbrTank * 40000));
                $usePlanet->setWorker($usePlanet->getWorker() - ($nbrTank * 5));
                $user->setBitcoin($user->getBitcoin() - ($nbrTank * 600));
                $usePlanet->setTankAt($now);
                $quest = $user->checkQuests('tank');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            if ($form_caserneRecruit->get('scientist')->getData() && $usePlanet->getCenterSearch() > 0) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrScientist = abs($form_caserneRecruit->get('scientist')->getData());
                if($nbrScientist > ($user->getBitcoin() / 250) ||
                    $nbrScientist > ($usePlanet->getWorker() / 10) ||
                    ($usePlanet->getWorker() < 5000 || ($usePlanet->getScientist() + $nbrScientist) > $usePlanet->getScientistMax())) {
                    return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                }
                if($usePlanet->getScientistAt()) {
                    if ($usePlanet->getScientist() + $usePlanet->getScientistAtNbr() + $nbrScientist > $usePlanet->getScientistMax()) {
                        return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
                    }
                    $tmpScientist = $usePlanet->getScientistAtNbr();
                    $now->add(new DateInterval('PT' . round((($nbrScientist + $tmpScientist) * 60)/ $user->getScientistProduction()) . 'S'));
                    $usePlanet->setScientistAtNbr($usePlanet->getScientistAtNbr() + $nbrScientist);
                } else {
                    $now->add(new DateInterval('PT' . round(($nbrScientist * 60)/ $user->getScientistProduction()) . 'S'));
                    $usePlanet->setScientistAtNbr($nbrScientist);
                }
                $usePlanet->setWorker($usePlanet->getWorker() - ($nbrScientist * 10));
                $user->setBitcoin($user->getBitcoin() - ($nbrScientist * 250));
                $usePlanet->setScientistAt($now);
                $quest = $user->checkQuests('scientist');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            $em->flush();
            return $this->redirectToRoute('soldier', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/soldier.html.twig', [
            'usePlanet' => $usePlanet,
            'form_caserneRecruit' => $form_caserneRecruit->createView()
        ]);
    }
}