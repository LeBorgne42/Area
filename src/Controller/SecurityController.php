<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Route("/login/", name="login_noSlash")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('interface');
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    /**
     * @Route("/logout", name="logout")
     * @Route("/logout/", name="logout_noSlash")
     */
    public function logoutAction(Request $request)
    {
    }

    /**
     * @Route("/login-redirect", name="login_redirect")
     * @Route("/login-redirect/", name="login_redirect_noSlash")
     */
    public function loginRedirectAction(Request $request)
    {
        if ($this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return $this->redirectToRoute('interface');
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('easyadmin');
        }
        return $this->redirectToRoute('logout');
    }
}