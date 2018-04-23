<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserRecoveryType;
use App\Entity\User;
use App\Entity\Rank;
use App\Entity\Research;
use App\Entity\Zearch_Demography;
use App\Entity\Zearch_Discipline;
use App\Entity\Zearch_HeavyShip;
use App\Entity\Zearch_Hyperespace;
use App\Entity\Zearch_Industry;
use App\Entity\Zearch_LightShip;
use App\Entity\Zearch_Onde;
use App\Entity\Zearch_Terraformation;
use App\Entity\Zearch_Utility;
use App\Entity\Zearch_Armement;
use App\Entity\Zearch_Missile;
use App\Entity\Zearch_Laser;
use App\Entity\Zearch_Plasma;
use App\Entity\Zearch_Cargo;
use App\Entity\Zearch_Recycleur;
use App\Entity\Zearch_Barge;
use App\Entity\Yhip_Colonizer;
use App\Entity\Yhip_Sonde;
use App\Entity\Yhip_Hunter;
use App\Entity\Yhip_Fregate;
use App\Entity\Yhip_Barge;
use App\Entity\Yhip_Recycleur;
use App\Entity\Ship;
use DateTime;
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
                        ->setParameters(array('ground' => 60, 'sky' => 10))
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

            $ship = new Ship();
            $colonizer = new Yhip_Colonizer();
            $vBarge = new Yhip_Barge();
            $vRecycleur = new Yhip_Recycleur();
            $sonde = new Yhip_Sonde();
            $hunter = new Yhip_Hunter();
            $fregate = new Yhip_Fregate();
            $ship->setColonizer($colonizer);
            $ship->setBarge($vBarge);
            $ship->setRecycleur($vRecycleur);
            $ship->setSonde($sonde);
            $ship->setHunter($hunter);
            $ship->setFregate($fregate);
            $em->persist($ship);
            $planet->setShip($ship);
            $em->persist($planet);
            $research = new Research();
            $demography = new Zearch_Demography();
            $discipline = new Zearch_Discipline();
            $heavyShip = new Zearch_HeavyShip();
            $lightShip = new Zearch_LightShip();
            $hyperespace = new Zearch_Hyperespace();
            $industry = new Zearch_Industry();
            $onde = new Zearch_Onde();
            $terraformation = new Zearch_Terraformation();
            $utility = new Zearch_Utility();
            $armement = new Zearch_Armement();
            $missile = new Zearch_Missile();
            $laser = new Zearch_Laser();
            $plasma = new Zearch_Plasma();
            $cargo = new Zearch_Cargo();
            $sRecycleur = new Zearch_Recycleur();
            $sBarge = new Zearch_Barge();
            $research->setDemography($demography);
            $research->setDiscipline($discipline);
            $research->setHeavyShip($heavyShip);
            $research->setLightShip($lightShip);
            $research->setHyperespace($hyperespace);
            $research->setIndustry($industry);
            $research->setOnde($onde);
            $research->setTerraformation($terraformation);
            $research->setUtility($utility);
            $research->setArmement($armement);
            $research->setMissile($missile);
            $research->setLaser($laser);
            $research->setPlasma($plasma);
            $research->setCargo($cargo);
            $research->setRecycleur($sRecycleur);
            $research->setBarge($sBarge);
            $em->persist($research);
            $user->setResearch($research);
            $rank = new Rank();
            $rank->setPoint(50);
            $em->persist($rank);
            $user->setRank($rank);
            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('Confirmation email'))
                ->setFrom('borntoswim42@gmail.com')
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

            $token = new UsernamePasswordToken(
                $user->getUsername(),
                null,
                'my_entity_user_provider',
                $user->getRoles()
            );

            $this->get('security.token_storage')->setToken($token);
            $request->getSession()->set('_security_main', serialize($token));

            $event = new InteractiveLoginEvent($request, $token);
            $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

            return $this->redirectToRoute('overview', array('idp' => $planet->getId()));
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