<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $user = 'Matt';
        return $this->render('index.html.twig', [
            'user' => $user,
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
