<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use App\Form\Front\PlanetRenameType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class OverviewController extends Controller
{
    /**
     * @Route("/empire/{idp}", name="overview", requirements={"idp"="\d+"})
     */
    public function overviewAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $fleetMove = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->where('f.user = :user')
            ->andWhere('f.planete is not null')
            ->setParameters(array('user' => $user))
            ->orderBy('f.flightTime')
            ->setMaxresults(5)
            ->getQuery()
            ->getResult();

        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);

        $form_manageRenamePlanet = $this->createForm(PlanetRenameType::class, $usePlanet);
        $form_manageRenamePlanet->handleRequest($request);

        if ($form_image->isSubmitted() && $form_image->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        if ($form_manageRenamePlanet->isSubmitted() && $form_manageRenamePlanet->isValid()) {
            $em->persist($usePlanet);
            $em->flush();
            return $this->redirectToRoute('overview', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/overview.html.twig', [
            'form_rename' => $form_manageRenamePlanet->createView(),
            'form_image' => $form_image->createView(),
            'usePlanet' => $usePlanet,
            'date' => $now,
            'fleetMove' => $fleetMove,
        ]);
    }
}