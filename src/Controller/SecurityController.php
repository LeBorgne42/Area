<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserRecoveryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/enregistrement", name="register")
     * @Route("/enregistrement/", name="register_noSlash")
     */
    public function registerAction()
    {
        return $this->render('security/register.html.twig');
    }

    /**
     * @Route("/nouveau-password", name="recoveryPw")
     * @Route("/nouveau-password/", name="recoveryPw_noSlash")
     */
    public function recoveryPwAction(Request $request, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $form_recoverPw = $this->createForm(UserRecoveryType::class);
        $form_recoverPw->handleRequest($request);

        if ($form_recoverPw->isSubmitted()) {
            $userPw = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.username = :pseudoEmail OR u.email = :pseudoEmail')
                ->setParameter('pseudoEmail', $form_recoverPw->get('pseudoEmail')->getData())
                ->getQuery()
                ->getOneOrNullResult();
            if($userPw) {
                $alpha = ['a', '8', 'c', '&', 'e', 'f', 'g', '5', 'i', '-', 'k', 'l', '(', 'n', 'o', 'M', 'A', 'F', ':', 'w', 'Z'];
                $newPassword = $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)];
                $userPw->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
                $em->persist($userPw);
                $em->flush();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('borntoswim42@gmail.com')
                    ->setTo($userPw->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/recoveryPw.html.twig',
                            array('password' => $newPassword)
                        ),
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute('home');
            }
        }

        return $this->render('security/recoveryPw.html.twig', [
            'form_recoverPw' => $form_recoverPw->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     * @Route("/login/", name="login_noSlash")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('overview');
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
            return $this->redirectToRoute('overview');
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('easyadmin');
        }
        return $this->redirectToRoute('logout');
    }

    /**
     * @Route("/confirmation-email", name="confirmEmail")
     * @Route("/confirmation-email/", name="confirmEmail_noSlash")
     */
    public function confirmEmailAction()
    {
        return $this->redirectToRoute('login');
    }
}