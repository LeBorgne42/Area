<?php

namespace App\Controller\Connected;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class AllPlanetsController extends AbstractController
{
    public function allPlanetsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $allPlanets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->select('p.id, p.name')
            ->where('p.user = :user')
            ->setParameters(['user' => $user])
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->json($allPlanets);
    }
}