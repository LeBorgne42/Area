<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Report;
use DateTime;
use DateTimeZone;
use DateInterval;

/**
 * @Route("/connect")
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

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $removeReports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.sendAt < :now')
            ->setParameters(['now' => $now])
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
            ->setParameters(['user' => $user])
            ->orderBy('r.sendAt', 'DESC')
            ->getQuery()
            ->getResult();

        $user->setViewReport(true);

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

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $report = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.id = :id')
            ->andWhere('r.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $report->setNewReport(false);

        $em->flush();

        return $this->redirectToRoute('report', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rapport-share/{idp}/{id}", name="report_share", requirements={"idp"="\d+", "id"="\d+"})
     */
    public function reportShareAction($idp, Report $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        if ($user == $id->getUser()) {
            $alpha = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-'];
            $newShareKey = $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)]
                . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)]
                . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)];

            $id->setShareKey($newShareKey);
            $em->flush();
        }

        return $this->redirectToRoute('report', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/report-view-all/{idp}/", name="report_all_view", requirements={"idp"="\d+"})
     */
    public function reportAllViewAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $reports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.newReport = :true')
            ->andWhere('r.user = :user')
            ->setParameters(['true' => true, 'user' => $user])
            ->getQuery()
            ->getResult();

        foreach($reports as $report) {
            $report->setNewReport(false);
        }
        $em->flush();

        return $this->redirectToRoute('report', ['idp' => $usePlanet->getId()]);
    }
}