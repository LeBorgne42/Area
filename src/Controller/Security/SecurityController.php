<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserRecoveryType;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class SecurityController extends Controller
{
    /**
     * @Route("/enregistrement", name="register")
     * @Route("/enregistrement/", name="register_noSlash")
     */
    public function registerAction(Request $request, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = new User();

        if ($_POST) {
            $alreadyInBase = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.username = :username')
                ->orWhere('u.email = :email')
                ->setParameters(['username' => $_POST['_username'], 'email' => $_POST['_email']])
                ->getQuery()
                ->getResult();

            foreach ($alreadyInBase as $check) {
                if (strtoupper($check->getUsername()) == strtoupper($_POST['_username'])) {
                    $this->addFlash("fail", "Ce pseudo existe déjà sur le jeu.");
                    return $this->redirectToRoute('home');
                } elseif (strtoupper($check->getEmail()) == strtoupper($_POST['_email'])) {
                    $this->addFlash("fail", "Un compte existe déjà avec cet email.");
                    return $this->redirectToRoute('home');
                }
            }

            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $user->setUsername($_POST['_username']);
            $user->setEmail($_POST['_email']);
            $user->setCreatedAt($now);
            $user->setPassword(password_hash($_POST['_password'], PASSWORD_BCRYPT));
            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('Confirmation email'))
                ->setFrom('support@areauniverse.eu')
                ->setTo($_POST['_email'])
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        [
                            'password' => $_POST['_password'],
                            'username' => $user->getUsername(),
                        ]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $token = new UsernamePasswordToken(
                $user,
                null,
                'main',
                $user->getRoles()
            );


           $this->get('security.token_storage')->setToken($token);
           $request->getSession()->set('_security_main', serialize($token));

           $event = new InteractiveLoginEvent($request, $token);
           $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

           return $this->redirectToRoute('login');
        }
        return $this->redirectToRoute('home');
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
                $em->flush();

                $message = (new \Swift_Message('Hello Email'))
                    ->setFrom('support@areauniverse.eu')
                    ->setTo($userPw->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/recoveryPw.html.twig',
                            ['password' => $newPassword]
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
    public function loginAction(AuthenticationUtils $authenticationUtils)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);


        if($user) {
            if($server->getOpen() == false && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
                return $this->redirectToRoute('pre_ally');
            }

            if($user->getGameOver()) {
                return $this->redirectToRoute('game_over');
            }

            $usePlanet = $em->getRepository('App:Planet')->findByFirstPlanet($user->getUsername());

            if($usePlanet) {
                return $this->redirectToRoute('overview', ['idp' => $usePlanet->getId(), 'usePlanet' => $usePlanet]);
            } else {
                $galaxys = $em->getRepository('App:Galaxy')
                    ->createQueryBuilder('g')
                    ->orderBy('g.position', 'ASC')
                    ->getQuery()
                    ->getResult();

                return $this->render('connected/play.html.twig', [
                    'galaxys' => $galaxys
                ]);
            }
        }

        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/logout", name="logout")
     * @Route("/logout/", name="logout_noSlash")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/deconnexion", name="disconnect_me")
     * @Route("/deconnexion/", name="disconnect_me_noSlash")
     */
    public function disconnectMeAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $user->setLastActivity(null);
        $em->flush();
        return $this->redirectToRoute('logout');
    }

    /**
     * @Route("/login-redirect", name="login_redirect")
     * @Route("/login-redirect/", name="login_redirect_noSlash")
     */
    public function loginRedirectAction()
    {
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);

        if($server->getOpen() == false && $this->getUser()->getRoles()[0] == 'ROLE_USER') {
            return $this->redirectToRoute('pre_ally');
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_USER') {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));

            if($user->getGameOver()) {
                return $this->redirectToRoute('game_over');
            }
            $usePlanet = $em->getRepository('App:Planet')->findByFirstPlanet($this->getUser()->getUsername());

            $user->setIpAddress($_SERVER['REMOTE_ADDR']);
            $user->setLastActivity($now);
            $em->flush();

            if($usePlanet) {
                return $this->redirectToRoute('overview', ['idp' => $usePlanet->getId(), 'usePlanet' => $usePlanet]);
            } else {
                $galaxys = $em->getRepository('App:Galaxy')
                    ->createQueryBuilder('g')
                    ->orderBy('g.position', 'ASC')
                    ->getQuery()
                    ->getResult();

                return $this->render('connected/play.html.twig', [
                    'galaxys' => $galaxys
                ]);
            }
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_MODO' || $this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('home');
        }
        return $this->redirectToRoute('logout');
    }

    /**
     * @Route("/confirmation-email/{key}", name="confirmEmail", requirements={"key"=".+"})
     * @Route("/confirmation-email/{key}/", name="confirmEmail_noSlash", requirements={"key"=".+"})
     */
    public function confirmEmailAction(Request $request, $key)
    {
        $userId = decrypt($key);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->find(['id' => $userId]);

        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $request->getSession()->set('main', serialize($token));

        return $this->redirectToRoute('overview');
    }
}