<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserContactType;
use DateTime;
use DateTimeZone;
use DateInterval;

class TopMenuController extends AbstractController
{
    /**
     * @Route("/reglement/{_locale}", name="rules", defaults={"_locale" = "fr"}, requirements={"_locale" = "fr|en|de"})
     * @Route("/reglement", name="rules_noSlash")
     */
    public function rulesAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
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
     * @Route("/medias/{_locale}", name="medias", defaults={"_locale" = "fr"}, requirements={"_locale" = "fr|en|de"})
     * @Route("/medias", name="medias_noSlash")
     */
    public function mediasAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/medias.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/but-du-jeu/{_locale}", name="point_game", defaults={"_locale" = "fr"}, requirements={"_locale" = "fr|en|de"})
     * @Route("/but-du-jeu", name="point_game_noSlash")
     */
    public function pointGameAction()
    {
        $em = $this->getDoctrine()->getManager();

        if($this->getUser()) {
            $usePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->join('p.user', 'u')
                ->where('u.username = :user')
                ->setParameters(['user' => $this->getUser()->getUsername()])
                ->getQuery()
                ->setMaxResults(1)
                ->getOneOrNullResult();
        } else {
            $usePlanet = null;
        }

        return $this->render('anonymous/point_game.html.twig', [
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/nous-contacter/{_locale}", name="contact", defaults={"_locale" = "fr"}, requirements={"_locale" = "fr|en|de"})
     * @Route("/nous-contacter", name="contact_noSlash")
     */
    public function contactAction(Request $request, \Swift_Mailer $mailer)
    {
        $form_contact = $this->createForm(UserContactType::class);
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted()) {
            $message = (new \Swift_Message('Reclamation joueur'))
                ->setFrom('support@areauniverse.eu')
                ->setTo('areauniverse.game@gmail.com')
                ->setBody(
                    $this->renderView(
                        'emails/contact.html.twig',
                        ['text' => $form_contact->get('text')->getData(), 'email' => $form_contact->get('email')->getData()]
                    ),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute('home');
        }

        return $this->render('anonymous/contact.html.twig', [
            'form_contact' => $form_contact->createView(),
        ]);
    }
}
