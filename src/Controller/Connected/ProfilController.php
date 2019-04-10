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
     * @Route("/profil-joueur/{userPlanet}/{usePlanet}", name="user_profil", requirements={"usePlanet"="\d+", "userPlanet"="\d+"})
     */
    public function userProfilAction(User $userPlanet, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->join('g.sectors', 's')
            ->join('s.planets', 'p')
            ->select('g.position')
            ->groupBy('g.id')
            ->where('p.user = :user')
            ->setParameters(['user' => $userPlanet])
            ->getQuery()
            ->getResult();

        return $this->render('connected/profil/player.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
            'galaxys' => $galaxys
        ]);
    }

    /**
     * @Route("/profil-alliance/{allyUser}/{usePlanet}", name="ally_profil", requirements={"usePlanet"="\d+", "allyUser"="\d+"})
     */
    public function allyProfilAction(Ally $allyUser, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }
        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->join('g.sectors', 's')
            ->join('s.planets', 'p')
            ->join('p.user', 'u')
            ->select('g.position')
            ->groupBy('g.id')
            ->where('u.ally = :ally')
            ->setParameters(['ally' => $allyUser])
            ->getQuery()
            ->getResult();

        return $this->render('connected/profil/ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
            'galaxys' => $galaxys
        ]);
    }

    /**
     * @Route("/profil-joueur-popup/{userPlanet}/{usePlanet}", name="user_profil_modal", requirements={"usePlanet"="\d+", "userPlanet"="\d+"})
     */
    public function userProfilModalAction(User $userPlanet, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }
        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->join('g.sectors', 's')
            ->join('s.planets', 'p')
            ->select('g.position')
            ->groupBy('g.id')
            ->where('p.user = :user')
            ->setParameters(['user' => $userPlanet])
            ->getQuery()
            ->getResult();

        return $this->render('connected/profil/modal_user.html.twig', [
            'usePlanet' => $usePlanet,
            'user' => $userPlanet,
            'galaxys' => $galaxys
        ]);
    }

    /**
     * @Route("/profil-alliance-popup/{allyUser}/{usePlanet}", name="ally_profil_modal", requirements={"usePlanet"="\d+", "allyUser"="\d+"})
     */
    public function allyProfilModalAction(Ally $allyUser, Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }
        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->join('g.sectors', 's')
            ->join('s.planets', 'p')
            ->join('p.user', 'u')
            ->select('g.position')
            ->groupBy('g.id')
            ->where('u.ally = :ally')
            ->setParameters(['ally' => $allyUser])
            ->getQuery()
            ->getResult();

        return $this->render('connected/profil/modal_ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
            'galaxys' => $galaxys
        ]);
    }
}