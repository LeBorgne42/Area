<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;
use DateInterval;

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
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->sub(new DateInterval('PT' . 1209600 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $removeReports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.sendAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();

        if($removeReports) {
            foreach($removeReports as $removeReport) {
                $em->remove($removeReport);
            }
        }
        $em->flush();

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

    /**
     * @Route("/report-view/{idp}/{id}", name="report_view", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function reportViewAction($idp, $id)
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

        $report = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.id = :id')
            ->andWhere('r.user = :user')
            ->setParameters(array('id' => $id, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $report->setNewReport(false);
        $em->persist($report);
        $em->flush();

        return $this->redirectToRoute('report', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/report-view-all/{idp}/", name="report_all_view", requirements={"idp"="\d+"})
     */
    public function reportAllViewAction($idp)
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
            ->where('r.newReport = :true')
            ->andWhere('r.user = :user')
            ->setParameters(array('true' => true, 'user' => $user))
            ->getQuery()
            ->getResult();

        foreach($reports as $report) {
            $report->setNewReport(false);
            $em->persist($report);
        }
        $em->flush();

        return $this->redirectToRoute('report', array('idp' => $usePlanet->getId()));
    }
}