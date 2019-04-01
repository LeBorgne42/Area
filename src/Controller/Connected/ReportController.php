<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Planet;
use App\Entity\Report;
use DateTime;
use DateTimeZone;
use DateInterval;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class ReportController extends AbstractController
{
    /**
     * @Route("/rapport/{usePlanet}", name="report", requirements={"usePlanet"="\d+"})
     * @Route("/rapport/{id}/{usePlanet}", name="report_id", requirements={"usePlanet"="\d+", "id"="\w+"})
     */
    public function reportAction(Planet $usePlanet, $id = 'defaut')
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->sub(new DateInterval('PT' . 2592000 . 'S'));
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $removeReports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.sendAt < :now')
            ->setParameters(['now' => $now])
            ->getQuery()
            ->getResult();

        if($removeReports) {
            foreach($removeReports as $removeReport) {
                $removeReport->setImageName(null);
                $em->remove($removeReport);
            }
        }
        $em->flush();

        if ($id == 'defaut') {
            $reports = $em->getRepository('App:Report')
                ->createQueryBuilder('r')
                ->where('r.user = :user')
                ->andWhere('r.type = :type')
                ->setParameters(['user' => $user, 'type' => 'defaut'])
                ->orderBy('r.sendAt', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $reports = $em->getRepository('App:Report')
                ->createQueryBuilder('r')
                ->where('r.user = :user')
                ->andWhere('r.type = :type')
                ->setParameters(['user' => $user, 'type' => $id])
                ->orderBy('r.sendAt', 'DESC')
                ->getQuery()
                ->getResult();
        }

        if ($id != 'defaut') {
            foreach($reports as $report) {
                if($report->getType() == $id && $report->getNewReport() == 2) {
                    $report->setNewReport(1);
                }
            }
        } else {
            foreach($reports as $report) {
                if($report->getType() == $id && $report->getNewReport() == 2) {
                    $report->setNewReport(1);
                }
            }
        }

        if(($user->getTutorial() == 1)) {
            $user->setTutorial(2);
        }
        if(($user->getTutorial() == 21)) {
            $user->setTutorial(22);
        }

        $em->flush();

        return $this->render('connected/report.html.twig', [
            'usePlanet' => $usePlanet,
            'reports' => $reports,
            'reportPage' => $id
        ]);
    }

    /**
     * @Route("/report-view/{report}/{usePlanet}", name="report_view", requirements={ "report"="\d+", "usePlanet"="\d+"})
     */
    public function reportViewAction(Planet $usePlanet, Report $report)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user || $report->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $report->setNewReport(0);

        $em->flush();

        return $this->redirectToRoute('report_id', ['id' => $report->getType(), 'usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/supprimer-rapport/{report}/{usePlanet}", name="report_delete", requirements={"usePlanet"="\d+", "report"="\d+"})
     */
    public function reportDeleteAction(Planet $usePlanet, Report $report)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user || $report->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $report->setImageName(null);
        $path = $report->getType();
        $em->remove($report);
        $em->flush();

        return $this->redirectToRoute('report_id', ['id' => $path, 'usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rapport-share/{id}/{usePlanet}", name="report_share", requirements={"usePlanet"="\d+", "id"="\d+"})
     */
    public function reportShareAction(Planet $usePlanet, Report $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }
        if ($user == $id->getUser()) {
            $alpha = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-'];
            $newShareKey = $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)]
                . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)]
                . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)] . $alpha[rand(0, 36)];

            $id->setShareKey($newShareKey);
            $em->flush();
        }

        return $this->redirectToRoute('report_id', ['id' => $id->getType(), 'usePlanet' => $usePlanet->getId()]);
    }

    /**
     * @Route("/report-view-all/{usePlanet}/", name="report_all_view", requirements={"usePlanet"="\d+"})
     */
    public function reportAllViewAction(Planet $usePlanet)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if ($usePlanet->getUser() != $user) {
            return $this->redirectToRoute('home');
        }

        $reports = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->where('r.newReport = :one')
            ->andWhere('r.user = :user')
            ->setParameters(['user' => $user, 'one' => 1])
            ->getQuery()
            ->getResult();

        foreach($reports as $report) {
            $report->setNewReport(0);
        }
        $em->flush();

        return $this->redirectToRoute('report', ['usePlanet' => $usePlanet->getId()]);
    }
}