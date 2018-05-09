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
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $form_caserneRecruit = $this->createForm(CaserneRecruitType::class);
        $form_caserneRecruit->handleRequest($request);

        $form_scientistRecruit = $this->createForm(ScientistRecruitType::class);
        $form_scientistRecruit->handleRequest($request);

        if ($form_caserneRecruit->isSubmitted() && $form_caserneRecruit->isValid()) {
            if($form_caserneRecruit->get('soldier')->getData() > ($user->getBitcoin() / 10) ||
                ($form_caserneRecruit->get('soldier')->getData() > $usePlanet->getWorker() || ($usePlanet->getSoldier() + $form_caserneRecruit->get('soldier')->getData()) > $usePlanet->getSoldierMax()) ||
                ($usePlanet->getWorker() < 5000)) {
                return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
            }
            if($usePlanet->getSoldierAt()) {
                if ($usePlanet->getSoldierAtNbr() + $form_caserneRecruit->get('soldier')->getData() > $usePlanet->getSoldierMax()) {
                    return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
                }
                $tmpSoldier = $usePlanet->getSoldierAtNbr() - $usePlanet->getSoldier();
                $now->add(new DateInterval('PT' . ((($form_caserneRecruit->get('soldier')->getData() + $tmpSoldier))) . 'S'));
                $usePlanet->setSoldierAtNbr($usePlanet->getSoldierAtNbr() + $form_caserneRecruit->get('soldier')->getData());
            } else {
                $now->add(new DateInterval('PT' . $form_caserneRecruit->get('soldier')->getData() . 'S'));
                $usePlanet->setSoldierAtNbr($usePlanet->getSoldier() + $form_caserneRecruit->get('soldier')->getData());
            }
            $usePlanet->setWorker($usePlanet->getWorker() - $form_caserneRecruit->get('soldier')->getData());
            $usePlanet->setSoldierAt($now);
            $user->setBitcoin($user->getBitcoin() - ($form_caserneRecruit->get('soldier')->getData() * 10));
            $em->persist($usePlanet);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
        }

        if ($form_scientistRecruit->isSubmitted() && $form_scientistRecruit->isValid()) {
            if($form_scientistRecruit->get('scientist')->getData() > ($user->getBitcoin() / 100) ||
                $form_scientistRecruit->get('scientist')->getData() > ($usePlanet->getWorker() / 2) ||
                ($usePlanet->getWorker() < 5000 || ($usePlanet->getScientist() + $form_scientistRecruit->get('scientist')->getData()) > $usePlanet->getScientistMax())) {
                return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
            }
            if($usePlanet->getScientistAt()) {
                if ($usePlanet->getScientistAtNbr() + $form_scientistRecruit->get('scientist')->getData() > $usePlanet->getScientistMax()) {
                    return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
                }
                $tmpScientist = $usePlanet->getScientistAtNbr() - $usePlanet->getScientist();
                $now->add(new DateInterval('PT' . round((($form_scientistRecruit->get('scientist')->getData() + $tmpScientist) * 60)/ $user->getScientistProduction()) . 'S'));
                $usePlanet->setScientistAtNbr($usePlanet->getScientistAtNbr() + $form_scientistRecruit->get('scientist')->getData());
            } else {
                $now->add(new DateInterval('PT' . round(($form_scientistRecruit->get('scientist')->getData() * 60)/ $user->getScientistProduction()) . 'S'));
                $usePlanet->setScientistAtNbr($usePlanet->getScientist() + $form_scientistRecruit->get('scientist')->getData());
            }
            $usePlanet->setWorker($usePlanet->getWorker() - ($form_scientistRecruit->get('scientist')->getData() * 2));
            $user->setBitcoin($user->getBitcoin() - ($form_scientistRecruit->get('scientist')->getData() * 100));
            $usePlanet->setScientistAt($now);
            $em->persist($usePlanet);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/soldier.html.twig', [
            'usePlanet' => $usePlanet,
            'form_caserneRecruit' => $form_caserneRecruit->createView(),
            'form_scientistRecruit' => $form_scientistRecruit->createView(),
        ]);
    }
}