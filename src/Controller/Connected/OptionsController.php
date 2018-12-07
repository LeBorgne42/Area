<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\ModifPasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class OptionsController extends AbstractController
{
    /**
     * @Route("/mes-options/{idp}", name="my_options", requirements={"idp"="\d+"})
     */
    public function optionsAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_password = $this->createForm(ModifPasswordType::class);
        $form_password->handleRequest($request);

        if ($form_password->isSubmitted() && $form_password->isValid()) {
            if(password_verify($form_password->get('oldPassword')->getData(), $user->getPassword())) {
                if(count($form_password->get('password')->getData()) == 1 && $form_password->get('password')->getData() == $form_password->get('confirmPassword')->getData()) {
                    $user->setPassword(password_hash($form_password->get('password')->getData(), PASSWORD_BCRYPT));
                }
            }
            if($form_password->get('planetOrder')->getData()) {
                $user->setOrderPlanet($form_password->get('planetOrder')->getData());
            }

            $em->flush();
        }

        return $this->render('connected/options.html.twig', [
            'form_pass' => $form_password->createView(),
            'usePlanet' => $usePlanet,
        ]);
    }
}