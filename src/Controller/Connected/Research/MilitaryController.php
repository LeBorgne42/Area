<?php

namespace App\Controller\Connected\Research;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use Dateinterval;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class MilitaryController extends Controller
{
    /**
     * @Route("/rechercher-industrie/{idp}", name="research_industry", requirements={"idp"="\d+"})
     */
    public function researchIndustryAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getIndustry() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < 1500) ||
            ($level == 6 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 1500 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('industry');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 1500);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-vaisseaux-leger/{idp}", name="research_light_ship", requirements={"idp"="\d+"})
     */
    public function researchLightShipAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getLightShip() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < 9000 || $user->getIndustry() < 3) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 8600 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('lightShip');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 9000);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/rechercher-vaisseaux-lourd/{idp}", name="research_heavy_ship", requirements={"idp"="\d+"})
     */
    public function researchHeavyShipAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $level = $user->getHeavyShip() + 1;
        $userBt = $user->getBitcoin();

        if(($userBt < 42000 || $user->getIndustry() < 5) ||
            ($level == 4 || $user->getSearchAt() > $now)) {
            return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
        }

        $now->add(new DateInterval('PT' . ($level * 35000 / $user->getScientistProduction()) . 'S'));
        $user->setSearch('heavyShip');
        $user->setSearchAt($now);
        $user->setBitcoin($userBt - 42000);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('search', array('idp' => $usePlanet->getId()));
    }
}