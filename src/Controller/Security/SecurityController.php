<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserRecoveryType;
use App\Entity\User;
use App\Entity\Rank;
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
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $user->setUsername($_POST['_username']);
            $user->setEmail($_POST['_email']);
            $user->setCreatedAt($now);
            $user->setPassword(password_hash($_POST['_password'], PASSWORD_BCRYPT));

            $alreadyInBase = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.username = :username')
                ->orWhere('u.email = :email')
                ->setParameters(array('username' => $_POST['_username'], 'email' => $_POST['_email']))
                ->setMaxResults(1)
                ->getQuery()
                ->getResult();

            foreach ($alreadyInBase as $check) {
                if ($check->getUsername() == $_POST['_username']) {
                    $this->addFlash("fail", "Ce pseudo est déjà prit.");

                    return $this->redirectToRoute('home');
                } elseif ($check->getEmail() == $_POST['_email']) {
                    $this->addFlash("fail", "Il y a déjà un compte rattaché a cet email.");
                    return $this->redirectToRoute('login');
                }
            }

            $planet = $em->getRepository('App:Planet')
                        ->createQueryBuilder('p')
                        ->where('p.user is null')
                        ->andWhere('p.ground = :ground')
                        ->andWhere('p.sky = :sky')
                        ->andWhere('p.empty = :false')
                        ->andWhere('p.cdr = :false')
                        ->setParameters(array('ground' => 60, 'sky' => 10, 'false' => false))
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();
            if($planet) {
                $planet->setUser($user);
                $planet->setName('Nova Terra');
                $user->addPlanet($planet);
                $em->persist($planet);
            } else {
                return $this->redirectToRoute('logout');
            }

            $em->persist($planet);
            $rank = new Rank();
            $em->persist($rank);
            $user->setRank($rank);
            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('Confirmation email'))
                ->setFrom('areauniverse.game@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/registration.html.twig',
                        array(
                            'password' => $_POST['_password'],
                            'username' => $user->getUsername(),
                            )
                    ),
                    'text/html'
                );

            $mailer->send($message);


            return $this->redirectToRoute('login');

            /* $token = new UsernamePasswordToken(
                $user->getUsername(),
                null,
                'my_entity_user_provider',
                $user->getRoles()
            );


           $this->get('security.token_storage')->setToken($token);
            $request->getSession()->set('_security_main', serialize($token));

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirectToRoute('overview', array('idp' => $planet->getId()));*/
        }
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
                    ->setFrom('areauniverse.game@gmail.com')
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
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(array('user' => $this->getUser()->getUsername()))
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            return $this->redirectToRoute('overview', array('idp' => $usePlanet->getId(), 'usePlanet' => $usePlanet));
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
    public function logoutAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $user->setConnected(false);
        $em->persist($user);
        $em->flush();
    }

    /**
     * @Route("/login-redirect", name="login_redirect")
     * @Route("/login-redirect/", name="login_redirect_noSlash")
     */
    public function loginRedirectAction()
    {
        if ($this->getUser()->getRoles()[0] == 'ROLE_USER') {
            $user = $this->getUser();
            $em = $this->getDoctrine()->getManager();

            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(array('user' => $this->getUser()->getUsername()))
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();

            $user->setConnected(true);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('overview', array('idp' => $usePlanet->getId(), 'usePlanet' => $usePlanet));
        }
        if ($this->getUser()->getRoles()[0] == 'ROLE_MODO' || $this->getUser()->getRoles()[0] == 'ROLE_ADMIN') {
            return $this->redirectToRoute('easyadmin');
        }
        return $this->redirectToRoute('logout');
    }

    /**
     * @Route("/confirmation-email/{key}", name="confirmEmail", requirements={"key"=".+"})
     * @Route("/confirmation-email/{key}/", name="confirmEmail_noSlash", requirements={"key"=".+"})
     */
    public function confirmEmailAction(Request $request, $key)
    {
        exit;
        $userId = decrypt($key);
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        $token = new UsernamePasswordToken(
            $user->getUsername(),
            $user->getPassword(),
            'my_entity_user_provider',
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $request->getSession()->set('main', serialize($token));

        return $this->redirectToRoute('overview');
    }
}