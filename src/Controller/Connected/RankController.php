<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class RankController extends AbstractController
{
    /**
     * @Route("/classement-alliance/{idp}", name="rank_ally", requirements={"idp"="\d+"})
     */
    public function rankAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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
     * @Route("/classement-joueurs/{idp}", name="rank_user", requirements={"idp"="\d+"})
     */
    public function rankUserAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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
//     * @Route("/classement-joueurs/{idp}", name="rank_user", requirements={"idp"="\d+"})
//     */
//    public function rankUserAction($idp)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $user = $this->getUser();
//        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
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