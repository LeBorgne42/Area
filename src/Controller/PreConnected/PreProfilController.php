<?php

namespace App\Controller\PreConnected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class PreProfilController extends Controller
{
    /**
     * @Route("/pre-profil-joueur/{id}", name="pre_user_profil")
     */
    public function userProfilAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $userPlanet = $em->getRepository('App:User')->find(['id' => $id]);

        return $this->render('preconnected/profil/player.html.twig', [
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/pre-profil-alliance/{id}", name="pre_ally_profil")
     */
    public function allyProfilAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $allyUser = $em->getRepository('App:Ally')->find(['id' => $id]);

        return $this->render('preconnected/profil/ally.html.twig', [
            'ally' => $allyUser,
        ]);
    }

    /**
     * @Route("/pre-profil-joueur-popup/{id}", name="pre_user_profil_modal")
     */
    public function userProfilModalAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $userPlanet = $em->getRepository('App:User')->find(['id' => $id]);

        return $this->render('preconnected/profil/modal_user.html.twig', [
            'user' => $userPlanet,
        ]);
    }

    /**
     * @Route("/pre-profil-alliance-popup/{id}", name="pre_ally_profil_modal")
     */
    public function allyProfilModalAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $allyUser = $em->getRepository('App:Ally')->find(['id' => $id]);

        return $this->render('preconnected/profil/modal_ally.html.twig', [
            'ally' => $allyUser,
        ]);
    }
}