<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class UniverseController extends Controller
{
    /**
     * @Route("/univers", name="universe")
     * @Route("/univers/", name="universe_withSlash")
     */
    public function universeAction()
    {
        $em = $this->getDoctrine()->getManager();

        $galaxys = $em->getRepository('App:Galaxy')
            ->createQueryBuilder('g')
            ->orderBy('g.position', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/universe.html.twig', [
            'galaxys' => $galaxys,
        ]);
    }
}