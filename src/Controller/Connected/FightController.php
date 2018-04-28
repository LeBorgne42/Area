<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class FightController extends Controller
{
    /**
     * @Route("/weNeedABigBigFight/", name="fight_war")
     */
    public function fightAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $fleetsWar = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.fightAt < :now')
            ->setParameters(array('now' => $now))
            ->getQuery()
            ->getResult();
        foreach ($fleetsWar as $mdr) {
        }
        exit;
    }
}