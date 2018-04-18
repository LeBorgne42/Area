<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SoldierController extends Controller
{
    /**
     * @Route("/entrainement/{idp}", name="soldier", requirements={"idp"="\d+"})
     */
    public function soldierAction($idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/soldier.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }
}