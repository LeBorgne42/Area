<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\CaserneRecruitType;
use App\Form\Front\ScientistRecruitType;
use App\Entity\Planet;
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
     * @Route("/entrainement/{usePlanet}", name="soldier", requirements={"usePlanet"="\d+"})
     */
    public function soldierAction(Request $request, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $form_caserneRecruit = $this->createForm(CaserneRecruitType::class);
        $form_caserneRecruit->handleRequest($request);

        if ($form_caserneRecruit->isSubmitted() && $form_caserneRecruit->isValid()) {
            if ($form_caserneRecruit->get('soldier')->getData()) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrSoldier = abs($form_caserneRecruit->get('soldier')->getData());
                if ($user->getAlly()) {
                    if ($user->getPoliticSoldierSale() > 0) {
                        $price = 75 - ($user->getPoliticSoldierSale() * 5);
                    } else {
                        $price = 75;
                    }
                }
                if($nbrSoldier * $price > $user->getBitcoin() ||
                    ($nbrSoldier * 2 > $usePlanet->getWorker() || ($usePlanet->getSoldier() + $nbrSoldier) > $usePlanet->getSoldierMax()) ||
                    ($usePlanet->getWorker() < 10000)) {
                    if ($nbrSoldier * 2 > $usePlanet->getWorker()) {
                        $this->addFlash("fail", "Vous n'avez pas assez de travailleurs.");
                    } elseif ($usePlanet->getSoldier() + $nbrSoldier > $usePlanet->getSoldierMax()) {
                        $this->addFlash("fail", "Vous dépassez la limite de soldats sur la planète.");
                    } elseif ($usePlanet->getWorker() < 10000) {
                        $this->addFlash("fail", "Une planète ne peut avoir moins de 10000 travailleurs.");
                    } elseif ($nbrSoldier * $price > $user->getBitcoin()) {
                        $this->addFlash("fail", "Vous ne disposez pas d'assez de bitcoins.");
                    } else {
                        $this->addFlash("fail", "Vous n'avez pas toutes les conditions requises.");
                    }
                    return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
                }
                if($usePlanet->getSoldierAt()) {
                    if ($usePlanet->getSoldier() + $usePlanet->getSoldierAtNbr() + $nbrSoldier > $usePlanet->getSoldierMax()) {
                        $this->addFlash("fail", "Vous dépassez la limite de soldats sur la planète.");
                        return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
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
                $user->setBitcoin($user->getBitcoin() - ($nbrSoldier * $price));
                $quest = $user->checkQuests('soldier');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            if ($form_caserneRecruit->get('tank')->getData() && ($usePlanet->getBunker() > 0 || $usePlanet->getCaserne() > 0) && $usePlanet->getLightUsine() > 0 && $user->getTank() == 1) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrTank = abs($form_caserneRecruit->get('tank')->getData());
                if($nbrTank * 600 > $user->getBitcoin() ||
                    $nbrTank * 5 > $usePlanet->getWorker() ||
                    $nbrTank * 40000 > $usePlanet->getNiobium() ||
                    ($usePlanet->getWorker() < 10000 || ($usePlanet->getTank() + $nbrTank) > 500)) {
                    if ($nbrTank * 5 > $usePlanet->getWorker()) {
                        $this->addFlash("fail", "Vous n'avez pas assez de travailleurs.");
                    } elseif ($nbrTank * 600 > $user->getBitcoin()) {
                        $this->addFlash("fail", "Vous ne disposez pas d'assez de bitcoins.");
                    } elseif ($nbrTank * 40000 > $usePlanet->getNiobium()) {
                        $this->addFlash("fail", "Vous ne disposez pas d'assez de niobiums.");
                    } elseif ($usePlanet->getTank() + $nbrTank > 500) {
                        $this->addFlash("fail", "Vous dépassez la limite de tanks sur la planète.");
                    } elseif ($usePlanet->getWorker() < 10000) {
                        $this->addFlash("fail", "Une planète ne peut avoir moins de 10000 travailleurs.");
                    } else {
                        $this->addFlash("fail", "Vous n'avez pas toutes les conditions requises.");
                    }
                    return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
                }
                if($usePlanet->getTankAt()) {
                    if ($usePlanet->getTank() + $usePlanet->getTankAtNbr() + $nbrTank > 500) {
                        $this->addFlash("fail", "Vous dépassez la limite de tanks sur la planète.");
                        return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
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
            if ($form_caserneRecruit->get('nuclear')->getData() && $usePlanet->getNuclearBase() > 0) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrNuclear = abs($form_caserneRecruit->get('nuclear')->getData());
                if($nbrNuclear > $user->getBitcoin() / 25000 ||
                    $nbrNuclear * 1000 > $usePlanet->getUranium() ||
                    ($usePlanet->getWorker() < 10000 || ($usePlanet->getNuclearBomb() + $nbrNuclear) > $usePlanet->getNuclearBase())) {
                    if ($nbrNuclear * 1000 > $usePlanet->getUranium()) {
                        $this->addFlash("fail", "Vous n'avez pas assez d'uranium.");
                    } elseif ($nbrNuclear > $user->getBitcoin() / 25000) {
                        $this->addFlash("fail", "Vous ne disposez pas d'assez de bitcoins.");
                    } elseif ($usePlanet->getNuclearBomb() + $nbrNuclear > $usePlanet->getNuclearBase()) {
                        $this->addFlash("fail", "Vous dépassez la limite de bombes nucléaires sur la planète.");
                    } elseif ($usePlanet->getWorker() < 10000) {
                        $this->addFlash("fail", "Une planète ne peut avoir moins de 10000 travailleurs.");
                    } else {
                        $this->addFlash("fail", "Vous n'avez pas toutes les conditions requises.");
                    }
                    return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
                }
                if($usePlanet->getNuclearAt()) {
                    if ($usePlanet->getNuclearBomb() + $usePlanet->getNuclearAtNbr() + $nbrNuclear > $usePlanet->getNuclearBase()) {
                        $this->addFlash("fail", "Vous dépassez la limite de bombes nucléaires sur la planète.");
                        return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
                    }
                    $tmpNuclear = $usePlanet->getNuclearAtNbr();
                    $now->add(new DateInterval('PT' . round(($nbrNuclear + $tmpNuclear) * 60) . 'S'));
                    $usePlanet->setNuclearAtNbr($usePlanet->getNuclearAtNbr() + $nbrNuclear);
                } else {
                    $now->add(new DateInterval('PT' . round($nbrNuclear * 24) . 'H'));
                    $usePlanet->setNuclearAtNbr($nbrNuclear);
                }
                $usePlanet->setUranium($usePlanet->getUranium() - ($nbrNuclear * 1000));
                $user->setBitcoin($user->getBitcoin() - ($nbrNuclear * 25000));
                $usePlanet->setNuclearAt($now);
                $quest = $user->checkQuests('nuclear');
                if($quest) {
                    $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                    $user->removeQuest($quest);
                }
            }
            if ($form_caserneRecruit->get('scientist')->getData() && $usePlanet->getCenterSearch() > 0) {
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));
                $nbrScientist = abs($form_caserneRecruit->get('scientist')->getData());
                if($nbrScientist > $user->getBitcoin() / 250 ||
                    $nbrScientist * 10 > $usePlanet->getWorker() ||
                    ($usePlanet->getWorker() < 10000 || ($usePlanet->getScientist() + $nbrScientist) > $usePlanet->getScientistMax())) {
                    if ($nbrScientist * 10 > $usePlanet->getWorker()) {
                        $this->addFlash("fail", "Vous n'avez pas assez de travailleurs.");
                    } elseif ($nbrScientist > $user->getBitcoin() / 250) {
                        $this->addFlash("fail", "Vous ne disposez pas d'assez de bitcoins.");
                    } elseif ($usePlanet->getScientist() + $nbrScientist > $usePlanet->getScientistMax()) {
                        $this->addFlash("fail", "Vous dépassez la limite de scientifiques sur la planète.");
                    } elseif ($usePlanet->getWorker() < 10000) {
                        $this->addFlash("fail", "Une planète ne peut avoir moins de 10000 travailleurs.");
                    } else {
                        $this->addFlash("fail", "Vous n'avez pas toutes les conditions requises.");
                    }
                    return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
                }
                if($usePlanet->getScientistAt()) {
                    if ($usePlanet->getScientist() + $usePlanet->getScientistAtNbr() + $nbrScientist > $usePlanet->getScientistMax()) {
                        $this->addFlash("fail", "Vous dépassez la limite de scientifiques sur la planète.");
                        return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
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
            return $this->redirectToRoute('soldier', ['usePlanet' => $usePlanet->getId()]);
        }

        return $this->render('connected/soldier.html.twig', [
            'usePlanet' => $usePlanet,
            'form_caserneRecruit' => $form_caserneRecruit->createView()
        ]);
    }
}