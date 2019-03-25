<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class RankController extends AbstractController
{
    /**
     * @Route("/classement-alliance/{usePlanet}", name="rank_ally", requirements={"usePlanet"="\d+"})
     */
    public function rankAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $allAllys = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.rank is not null')
            ->orderBy('a.rank', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/ally/rank.html.twig', [
            'usePlanet' => $usePlanet,
            'allAllys' => $allAllys,
        ]);
    }

    /**
     * @Route("/classement-joueurs/{usePlanet}", name="rank_user", requirements={"usePlanet"="\d+"})
     */
    public function rankUserAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->select('a.id as alliance, a.sigle as sigle, u.id, u.username, r.warPoint as warPoint, u.createdAt')
            ->leftJoin('u.rank', 'r')
            ->leftJoin('u.ally', 'a')
            ->where('u.rank is not null')
            ->andWhere('u.id != :one')
            ->andWhere('r.warPoint > :one')
            ->setParameters(['one' => 1])
            ->orderBy('r.warPoint', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/rank.html.twig', [
            'usePlanet' => $usePlanet,
            'users' => $users,
        ]);
    }

//    /**
//     * @Route("/classement-joueurs/{usePlanet}", name="rank_user", requirements={"usePlanet"="\d+"})
//     */
//    public function rankUserAction(Planet $usePlanet)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $user = $this->getUser();
//        if ($usePlanet->getUser() != $user) {
//          return $this->redirectToRoute('home');
//        }
//
//        $users = $em->getRepository('App:User')
//            ->createQueryBuilder('u')
//            ->leftJoin('u.rank', 'r')
//            ->where('u.rank is not null')
//            ->andWhere('u.id != :one')
//            ->setParameters(array('one' => 1))
//            ->orderBy('r.point', 'DESC')
//            ->getQuery()
//            ->getResult();
//
//        return $this->render('connected/rank.html.twig', [
//            'usePlanet' => $usePlanet,
//            'users' => $users,
//        ]);
//    }
}