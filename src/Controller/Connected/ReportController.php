<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class ReportController extends Controller
{
    /**
     * @Route("/rapport/{idp}", name="report", requirements={"idp"="\d+"})
     */
    public function reportAction($idp)
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

        $reports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameters(array('user' => $user))
            ->orderBy('r.sendAt', 'DESC')
            ->getQuery()
            ->getResult();

        $user->setViewReport(true);
        $em->persist($user);
        $em->flush();

        return $this->render('connected/report.html.twig', [
            'usePlanet' => $usePlanet,
            'reports' => $reports,
        ]);
    }
}