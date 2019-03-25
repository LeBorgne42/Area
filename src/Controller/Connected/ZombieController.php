<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\MissionType;
use App\Entity\Planet;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ZombieController extends AbstractController
{
    /**
     * @Route("/zombie/{usePlanet}", name="zombie", requirements={"usePlanet"="\d+"})
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

        $planet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy('p.ground', 'ASC')
            ->getQuery()
            ->setMaxresults(1)
            ->getOneOrNullResult();

        $form_missionUranium = $this->createForm(MissionType::class);
        $form_missionUranium->handleRequest($request);

        $form_missionZombie = $this->createForm(MissionType::class);
        $form_missionZombie->handleRequest($request);

        if ($form_missionUranium->isSubmitted() && $form_missionUranium->isValid()) {

            $em->flush();
        }

        if ($form_missionZombie->isSubmitted() && $form_missionZombie->isValid()) {

            $em->flush();
        }
        return $this->render('connected/zombie.html.twig', [
            'usePlanet' => $usePlanet,
            'planet' => $planet,
            'form_missionZombie' => $form_missionZombie->createView(),
            'form_missionUranium' => $form_missionUranium->createView()
        ]);
    }
}