<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserContactType;

class TopMenuController extends Controller
{
    /**
     * @Route("/media/", name="media")
     * @Route("/media/", name="media_noSlash")
     */
    public function mediaAction()
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
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/media.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/reglement/", name="rules")
     * @Route("/reglement/", name="rules_noSlash")
     */
    public function rulesAction()
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
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/rules.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/faq/", name="faq")
     * @Route("/faq/", name="faq_noSlash")
     */
    public function faqAction()
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
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/faq.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/nous-contacter/", name="contact")
     * @Route("/nous-contacter/", name="contact_noSlash")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $form_contact = $this->createForm(UserContactType::class);
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted()) {
            $message = (new \Swift_Message('Reclamation joueur'))
                ->setFrom('areauniverse.game@gmail.com')
                ->setTo('rivierematthieupro@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        array('text' => $form_contact->get('text')->getData(), 'email' => $form_contact->get('email')->getData())
                    ),
                    'text/html'
                );

            $mailer->send($message);

            $this->addFlash("success", "This is a success message");
            return $this->redirectToRoute('faq');
        }

        return $this->render('anonymous/contact.html.twig', [
            'form_contact' => $form_contact->createView(),
        ]);
    }
}
