<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class OverviewController extends Controller
{
    /**
     * @Route("/empire", name="overview")
     * @Route("/empire/", name="overview_withSlash")
     */
    public function overviewAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);
        if ($form_image->isSubmitted() && $form_image->isValid()) {
            $em->persist($user);
            $em->flush();
        }
        return $this->render('left_menu/overview.html.twig', [
            'form_image' => $form_image->createView(),
        ]);
    }
}