<?php

namespace App\Controller\Security;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SalonType;
use App\Entity\S_Content;
use App\Entity\Salon;
use DateTime;
use DateTimeZone;

/**
 * @Route("/private")
 * @Security("has_role('ROLE_PRIVATE')")
 */
class PrivateController extends Controller
{
    /**
     * @Route("/home", name="private_home")
     * @Route("/home/", name="private_home_noSlash")
     */
    public function privateHomeAction()
    {
        $user = $this->getUser();

        if($user) {
            if ($user->getRoles()[0] == 'ROLE_PRIVATE') {
                return $this->render('private/home.html.twig', [
                ]);
            }
        }
    }

    /**
     * @Route("/salon", name="private_salon")
     * @Route("/salon/", name="private_salon_noSlash")
     */
    public function privateSalonAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $salon = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.name = :name')
            ->setParameters(['name' => 'Private'])
            ->getQuery()
            ->getOneOrNullResult();

        $form_message = $this->createForm(SalonType::class);
        $form_message->handleRequest($request);

        if ($form_message->isSubmitted() && $form_message->isValid()) {
            $now = new DateTime();
            $now->setTimezone(new DateTimeZone('Europe/Paris'));
            $encrypt_method = "aes256";
            $secret_key = '°)Qfdd:M§¨¨èè!iV2dfgdfg&';
            $secret_iv = '°)!!èQ:Mghfg§¨g¨iV!!dfg&';
            $key = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);

            $message = new S_Content();
            $message->setSalon($salon);
            $message->setMessage(openssl_encrypt(nl2br($form_message->get('content')->getData() . 'd(kKd-&é°?,/+sSqwX@'), $encrypt_method, $key, false, $iv));
            $message->setSendAt($now);
            $message->setUser($user);
            $em->persist($message);
            $em->flush();

            return $this->render('private/salon.html.twig', [
                'salon' => $salon,
                'form_message' => $form_message->createView(),
            ]);
        }

        if($user) {
            if ($user->getRoles()[0] == 'ROLE_PRIVATE') {
                if ($_POST) {
                    if ($_POST['_username'] === 'raven' && $_POST['_password'] === 'flowers') {
                        if (!$salon) {
                            $ender = $em->getRepository('App:User')
                                ->createQueryBuilder('u')
                                ->where('u.username = :name')
                                ->setParameters(['name' => 'EndeR'])
                                ->getQuery()
                                ->getOneOrNullResult();


                            $thea = $em->getRepository('App:User')
                                ->createQueryBuilder('u')
                                ->where('u.username = :name')
                                ->setParameters(['name' => 'Thea'])
                                ->getQuery()
                                ->getOneOrNullResult();

                            $salon = new Salon();
                            $salon->setName('Private');
                            $salon->addUser($thea);
                            $salon->addUser($ender);
                            $em->persist($salon);
                            $em->flush();
                        }

                        return $this->render('private/salon.html.twig', [
                            'salon' => $salon,
                            'form_message' => $form_message->createView(),
                        ]);
                    }
                }
            }
        }
        return $this->redirectToRoute('private_home');
    }
}