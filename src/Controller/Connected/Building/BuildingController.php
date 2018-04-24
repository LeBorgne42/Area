<?php

namespace App\Controller\Connected\Building;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class BuildingController extends Controller
{
    /**
     * @Route("/batiment/{idp}", name="building", requirements={"idp"="\d+"})
     */
    public function buildingAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        return $this->render('connected/building.html.twig', [
            'usePlanet' => $usePlanet,
            'date' => $now,
        ]);
    }
}