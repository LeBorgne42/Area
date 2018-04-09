<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\User;
use App\Form\Front\UserRegisterType;
use App\Form\Front\UserRecoveryType;
use DateTime;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, AuthenticationUtils $authenticationUtils, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $user = new User();

        $form_register = $this->createForm(UserRegisterType::class,$user);
        $form_register->handleRequest($request);

        if ($form_register->isSubmitted() && $form_register->isValid()) {
            $user = $form_register->getData();
            $now = new DateTime();
            $user->setCreatedAt($now);
            $user->setImageName('default.png');
            $user->setImageSize(5);
            $user->setUpdatedAt($now);
            $user->setPassword(password_hash($form_register->get('password')->getData(), PASSWORD_BCRYPT));
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('register');
        }

        $form_recoverPw = $this->createForm(UserRecoveryType::class,$user);
        $form_recoverPw->handleRequest($request);

        if ($form_recoverPw->isSubmitted()) {
            $userPw = $em->getRepository('App:User')
                        ->createQueryBuilder('u')
                        ->where('u.username = :pseudoEmail OR u.email = :pseudoEmail')
                        ->setParameter('pseudoEmail', $form_recoverPw->get('pseudoEmail')->getData())
                        ->getQuery()
                        ->getOneOrNullResult();
            if($userPw) {
                $alpha = ['a', '8', 'c', '&', 'e', 'f', 'g', '5', 'i', '-', 'k', 'l', '(', 'n', 'o'];

                $newPassword = $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)];
                $userPw->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
                $em->persist($userPw);
                $em->flush();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('borntoswim42@gmail.com')
                    ->setTo($userPw->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/registration.html.twig',
                            array('password' => $newPassword)
                        ),
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute('home');
            }
        }

        return $this->render('index.html.twig', [
            'form_register' => $form_register->createView(),
            'form_recoverPw' => $form_recoverPw->createView(),
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/pw-recovery/", name="pw_recovery")
     * @Route("/pw-recovery", name="pw_recovery_noSlash")
     */
    public function pwRecoveryAction(Request $request, \Swift_Mailer $mailer)
    {
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('borntoswim42@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    array('name' => $user)
                ),
                'text/html'
            );

        $mailer->send($message);

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
