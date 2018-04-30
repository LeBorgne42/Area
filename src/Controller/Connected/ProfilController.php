<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $userPlanet = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameters(array('id' => $id))
            ->getQuery()
            ->getOneOrNullResult();

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $allyUser = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.id = :id')
            ->setParameters(array('id' => $id))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/profil/ally.html.twig', [
            'usePlanet' => $usePlanet,
            'ally' => $allyUser,
        ]);
    }
}