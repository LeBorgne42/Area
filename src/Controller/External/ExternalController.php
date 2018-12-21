<?php

namespace App\Controller\External;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ExternalController extends AbstractController
{
    /**
     * @Route("/nouveau-password/{key}", name="recoveryPw", requirements={"key"="\d+"})
     */
    public function recoveryPwAction(\Swift_Mailer $mailer, User $key)
    {
        $userId = $key; //fixmr decrypt
        $em = $this->getDoctrine()->getManager();
        if($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        $userPw = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.id = :user')
            ->setParameter('user', $userId)
            ->getQuery()
            ->getOneOrNullResult();

        if($userPw) {
            $alpha = ['a', '8', 'c', '&', 'e', 'f', 'g', '5', 'i', '-', 'k', 'l', '(', 'n', 'o', 'M', 'A', 'F', ':', 'w', 'Z'];
            $newPassword = $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)] . $alpha[rand(0, 14)];
            $userPw->setPassword(password_hash($newPassword, PASSWORD_BCRYPT));
            $em->flush();

            $message = (new \Swift_Message('Nouveau mot de passe'))
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
        }
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/confirmation-email/{key}", name="confirmEmail", requirements={"key"=".+"})
     * @Route("/confirmation-email/{key}/", name="confirmEmail_noSlash", requirements={"key"=".+"})
     */
    public function confirmEmailAction(Request $request, $key)
    {
        $userId = decrypt($key); //fixmr decrypt
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->find(['id' => $userId]);
        $user->setEmailConfirm(1);
        $em->flush();

        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $request->getSession()->set('main', serialize($token));

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/desactiver-newletter/{key}", name="deactivate_newletter", requirements={"key"=".+"})
     * @Route("/desactiver-newletter/{key}/", name="deactivate_newletter_noSlash", requirements={"key"=".+"})
     */
    public function deactivateNewletterAction(Request $request, $key)
    {
        $userId = decrypt($key); //fixmr decrypt
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->find(['id' => $userId]);
        $user->setNewletter(1);
        $em->flush();

        $token = new UsernamePasswordToken(
            $user,
            null,
            'main',
            $user->getRoles()
        );

        $this->get('security.token_storage')->setToken($token);
        $request->getSession()->set('main', serialize($token));

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/mail-admin", name="mail_while")
     * @Route("/mail-admin/", name="mail_while_noSlash")
     */
    public function mailAdminAction(\Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $users = $em->getRepository('App:User')->findAll();

        foreach($users as $user) {
            $message = (new \Swift_Message('Nouveau serveur 12/12/2018'))
                ->setFrom('support@areauniverse.eu')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'emails/new_server.html.twig',
                        [
                            'username' => $user->getUsername(),
                            'key' => $user->getId() //fixmr encrypt
                        ]
                    ),
                    'text/html'
                );
            $mailer->send($message);
        }

        return $this->redirectToRoute('home');
    }
}