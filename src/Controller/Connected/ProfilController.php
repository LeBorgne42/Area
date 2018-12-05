<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
 * @Security("has_role('ROLE_USER')")
 */
class ProfilController extends Controller
{
    /**
     * @Route("/profil-joueur/{idp}/{id}", name="user_profil", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function userProfilAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $userPlanet = $em->getRepository('App:User')->find(['id' => $id]);

        return $this->render('connected/profil/player.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/profil-alliance/{idp}/{id}", name="ally_profil", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function allyProfilAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $allyUser = $em->getRepository('App:Ally')->find(['id' => $id]);

        return $this->render('connected/profil/ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
        ]);
    }

    /**
     * @Route("/profil-joueur-popup/{idp}/{id}", name="user_profil_modal", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function userProfilModalAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $userPlanet = $em->getRepository('App:User')->find(['id' => $id]);

        return $this->render('connected/profil/modal_user.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/profil-alliance-popup/{idp}/{id}", name="ally_profil_modal", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function allyProfilModalAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $allyUser = $em->getRepository('App:Ally')->find(['id' => $id]);

        return $this->render('connected/profil/modal_ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
        ]);
    }
}