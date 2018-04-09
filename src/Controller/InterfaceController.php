<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/plop")
 * @Security("has_role('ROLE_USER')")
 */
class InterfaceController extends Controller
{
    /**
     * @Route("/interface", name="interface")
     * @Route("/interface/", name="interface_withSlash")
     */
    public function interfaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);
        if ($form_image->isSubmitted() && $form_image->isValid()) {
            $em->persist($user);
            $em->flush();
        }
        return $this->render('interface/overview.html.twig', [
            'form_image' => $form_image->createView(),
        ]);
    }
}