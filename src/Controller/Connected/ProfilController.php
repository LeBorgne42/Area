<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use App\Entity\User;
use App\Entity\Ally;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/profil-joueur/{usePlanet}/{userPlanet}", name="user_profil", requirements={"usePlanet"="\d+", "userPlanet"="\d+"})
     */
    public function userProfilAction(Planet $usePlanet, User $userPlanet)
    {
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/profil/player.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/profil-alliance/{usePlanet}/{allyUser}", name="ally_profil", requirements={"usePlanet"="\d+", "allyUser"="\d+"})
     */
    public function allyProfilAction(Planet $usePlanet, Ally $allyUser)
    {
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/profil/ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
        ]);
    }

    /**
     * @Route("/profil-joueur-popup/{usePlanet}/{userPlanet}", name="user_profil_modal", requirements={"usePlanet"="\d+", "userPlanet"="\d+"})
     */
    public function userProfilModalAction(Planet $usePlanet, User $userPlanet)
    {
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/profil/modal_user.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/profil-alliance-popup/{usePlanet}/{allyUser}", name="ally_profil_modal", requirements={"usePlanet"="\d+", "allyUser"="\d+"})
     */
    public function allyProfilModalAction(Planet $usePlanet, Ally $allyUser)
    {
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        return $this->render('connected/profil/modal_ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
        ]);
    }
}