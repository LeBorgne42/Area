<?php

namespace App\Controller\Connected\Research;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class PoliticController extends AbstractController
{
    /**
     * @Route("/lancer-recherche/{search}/{idp}", name="research_ally", requirements={"search"="\w+", "idp"="\d+"})
     */
    public function researchAllyAction($idp, $search)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $idp, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getWhichResearch($search) + 1;
        $userBt = $user->getBitcoin();
        $cost = $user->getResearchCost($search);
        $time = $user->getResearchTime($search);

        if(($userBt < ($level * $cost)) ||
            ($level == 6 || $user->getSearchAt() > $now) ||
            $user->getWhichResearch($search) === null) {
            return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
        }

        $now->add(new DateInterval('PT' . round(($level * $time / $user->getScientistProduction())) . 'S'));
        $user->setSearch($search);
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - ($level * $cost));
        $em->flush();

        return $this->redirectToRoute('search', ['idp' => $usePlanet->getId()]);
    }
}