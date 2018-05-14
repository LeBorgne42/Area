<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class RankController extends Controller
{
    /**
     * @Route("/classement-alliance/{idp}", name="rank_ally", requirements={"idp"="\d+"})
     */
    public function rankAction($idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $users = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.rank is not null')
            ->andWhere('u.id != :one')
            ->setParameters(array('one' => 1))
            ->orderBy('u.rank', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/rank.html.twig', [
            'usePlanet' => $usePlanet,
            'users' => $users,
        ]);
    }
}