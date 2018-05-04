<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\CaserneRecruitType;
use App\Form\Front\ScientistRecruitType;

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
                ($form_caserneRecruit->get('soldier')->getData() > $usePlanet->getWorker() || ($usePlanet->getSoldier() + $form_caserneRecruit->get('soldier')->getData()) > $usePlanet->getSoldierMax())) {
                return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
            }
            $usePlanet->setWorker($usePlanet->getWorker() - $form_caserneRecruit->get('soldier')->getData());
            $usePlanet->setSoldier($usePlanet->getSoldier() + $form_caserneRecruit->get('soldier')->getData());
            $user->setBitcoin($user->getBitcoin() - ($form_caserneRecruit->get('soldier')->getData() * 10));
            $em->persist($usePlanet);
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
        }

        if ($form_scientistRecruit->isSubmitted() && $form_scientistRecruit->isValid()) {
            if($form_scientistRecruit->get('scientist')->getData() > ($user->getBitcoin() / 100) ||
                $form_scientistRecruit->get('scientist')->getData() > ($usePlanet->getWorker() / 2) ) {
                return $this->redirectToRoute('soldier', array('idp' => $usePlanet->getId()));
            }
            $usePlanet->setWorker($usePlanet->getWorker() - ($form_scientistRecruit->get('scientist')->getData() * 2));
            $usePlanet->setScientist($usePlanet->getScientist() + $form_scientistRecruit->get('scientist')->getData());
            $user->setBitcoin($user->getBitcoin() - ($form_scientistRecruit->get('scientist')->getData() * 100));
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