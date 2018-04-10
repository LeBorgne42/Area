<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use App\Entity\User;
//use App\Form\Front\UserRegisterType;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $allUsers = $em->getRepository('App:User')
                        ->createQueryBuilder('u')
                        ->select('count(u)')
                        ->getQuery()
                        ->getSingleScalarResult();

//        $user = new User();
//        $form_register = $this->createForm(UserRegisterType::class,$user);
//        $form_register->handleRequest($request);
//
//        if ($form_register->isSubmitted() && $form_register->isValid()) {
//            $user = $form_register->getData();
//            $now = new DateTime();
//            $user->setCreatedAt($now);
//            $user->setPassword(password_hash($form_register->get('password')->getData(), PASSWORD_BCRYPT));
//            $em->persist($user);
//            $em->flush();
//
//            $message = (new \Swift_Message('Confirmation email'))
//                ->setFrom('borntoswim42@gmail.com')
//                ->setTo($user->getEmail())
//                ->setBody(
//                    $this->renderView(
//                        'emails/registration.html.twig',
//                        array('password' => $form_register->get('password')->getData())
//                    ),
//                    'text/html'
//                );
//
//            $mailer->send($message);
//
//            $this->addFlash("success", "This is a success message");
//            return $this->redirectToRoute('login');
//        }

        return $this->render('index.html.twig', [
//            'form_register' => $form_register->createView(),
            'allUsers' => $allUsers,
        ]);
    }
}
