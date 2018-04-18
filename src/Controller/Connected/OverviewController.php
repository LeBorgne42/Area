<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);
        if ($form_image->isSubmitted() && $form_image->isValid()) {
            $em->persist($user);
            $em->flush();
        }
        return $this->render('connected/overview.html.twig', [
            'form_image' => $form_image->createView(),
            'usePlanet' => $usePlanet,
        ]);
    }
}