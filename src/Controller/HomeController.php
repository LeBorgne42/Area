<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\User;
use App\Form\Front\UserRegisterType;
use App\Form\Front\UserConnectType;
use App\Form\Front\UserRecoveryType;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $user = new User();

        $form_register = $this->createForm(UserRegisterType::class,$user);
        $form_register->handleRequest($request);

        if ($form_register->isSubmitted() && $form_register->isValid()) {
            $user = $form_register->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('register');
        }

        $form_connect = $this->createForm(UserConnectType::class,$user);
        $form_connect->handleRequest($request);

        if ($form_connect->isSubmitted() && $form_connect->isValid()) {
            return $this->redirectToRoute('login');
        }

        $form_recoverPw = $this->createForm(UserRecoveryType::class,$user);
        $form_recoverPw->handleRequest($request);

        if ($form_recoverPw->isSubmitted() && $form_recoverPw->isValid()) {
            return $this->redirectToRoute('pw_recovery');
        }

        return $this->render('index.html.twig', [
            'form_register' => $form_register->createView(),
            'form_connect' => $form_connect->createView(),
            'form_recoverPw' => $form_recoverPw->createView(),
        ]);
    }

    /**
     * @Route("/login/", name="login")
     * @Route("/login", name="login_noSlash")
     */
    public function loginAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/pw-recovery/", name="pw_recovery")
     * @Route("/pw-recovery", name="pw_recovery_noSlash")
     */
    public function pwRecoveryAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/logout/", name="logout")
     * @Route("/logout/", name="logout_noSlash")
     */
    public function logoutAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/register/", name="register")
     * @Route("/register/", name="register_noSlash")
     */
    public function registerAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/media/", name="media")
     * @Route("/media/", name="media_noSlash")
     */
    public function mediaAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/faq/", name="faq")
     * @Route("/faq/", name="faq_noSlash")
     */
    public function faqAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/empire/", name="empire")
     * @Route("/empire/", name="empire_noSlash")
     */
    public function empireAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/hall_of_fame/", name="hall_of_fame")
     * @Route("/hall_of_fame/", name="hall_of_fame_noSlash")
     */
    public function hall_of_fameAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }
    /**
     * @Route("/donation/", name="donation")
     * @Route("/donation/", name="donation_noSlash")
     */
    public function donationAction(Request $request)
    {
        return $this->redirectToRoute('home');
    }
}
