<?php

namespace App\Controller\Share;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ShareController extends AbstractController
{
    /**
     * @Route("/rapport/share/{id}", name="report_share_display")
     */
    public function reportSharePageAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $report = $em->getRepository('App:Report')
            ->createQueryBuilder('r')
            ->andWhere('r.shareKey = :key')
            ->setParameters(['key' => $id])
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/share/report_share.html.twig', [
            'report' => $report,
        ]);
    }

    /**
     * @Route("/message/share/{id}", name="message_share_display")
     */
    public function messageSharePageAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $message = $em->getRepository('App:Message')
            ->createQueryBuilder('r')
            ->andWhere('r.shareKey = :key')
            ->setParameters(['key' => $id])
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/share/message_share.html.twig', [
            'message' => $message,
        ]);
    }

}