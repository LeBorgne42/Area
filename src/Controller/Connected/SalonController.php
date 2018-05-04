<?php

namespace App\Controller\Connected;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\SalonType;
use App\Entity\S_Content;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class SalonController extends Controller
{
    /**
     * @Route("/salon/{idp}", name="salon", requirements={"idp"="\d+"})
     * @Route("/salon/{idp}/{salon}", name="salon_id", requirements={"idp"="\d+"})
     */
    public function salonAction(Request $request, $idp, $salon = null)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $salons = $em->getRepository('App:Salon')
            ->createQueryBuilder('s')
            ->where('s.ally = :ally')
            ->orWhere('s.id = :id')
            ->setParameters(array('ally' => $user->getAlly(), 'id' => 1))
            ->getQuery()
            ->getResult();

        $form_message = $this->createForm(SalonType::class);
        $form_message->handleRequest($request);

        /* if($request->isXmlHttpRequest()) {
            if ($_POST['newMessage'] != "") {
                $attachSalon = $em->getRepository('App:Salon')
                    ->createQueryBuilder('s')
                    ->where('s.id = :id')
                    ->setParameters(array('id' => $salon))
                    ->getQuery()
                    ->getOneOrNullResult();

                $message = new S_Content();
                $message->setSalon($attachSalon);
                $message->setMessage(nl2br($_POST['newMessage']));
                $message->setSendAt($now);
                $message->setUser($user);

                if(count($attachSalon->getMessages()) > 50) {
                    $removeMessage = $em->getRepository('App:S_Content')
                        ->createQueryBuilder('sc')
                        ->setParameters(array('id' => $salon))
                        ->orderBy('sc.sendAt', 'ASC')
                        ->setMaxResults('10')
                        ->getQuery()
                        ->getResult();
                    foreach($removeMessage as $oneMessage) {
                        $em->remove($oneMessage);
                    }
                }

                $em->persist($message);
                $em->flush();

               $response = new JsonResponse();
                $response->setData(
                    array(
                        'has_error' => false,
                        'messages' => $message,
                    )
                );
                return $response;
            }*/
           /* else {
                $response = new JsonResponse();
                $response->setData(
                    array(
                        'has_error' => false,
                        'messages' => ,
                    )
                );
                return $response;
            }
        }*/

        if ($form_message->isSubmitted() && $form_message->isValid()) {
            $attachSalon = $em->getRepository('App:Salon')
                            ->createQueryBuilder('s')
                            ->where('s.id = :id')
                            ->setParameters(array('id' => $salon))
                            ->getQuery()
                            ->getOneOrNullResult();

            $message = new S_Content();
            $message->setSalon($attachSalon);
            $message->setMessage(nl2br($form_message->get('content')->getData()));
            $message->setSendAt($now);
            $message->setUser($user);

            if(count($attachSalon->getContents()) > 50) {
                $removeMessage = $em->getRepository('App:S_Content')
                    ->createQueryBuilder('sc')
                    ->orderBy('sc.sendAt', 'ASC')
                    ->setMaxResults('10')
                    ->getQuery()
                    ->getResult();
                foreach($removeMessage as $oneMessage) {
                    $em->remove($oneMessage);
                }
            }

            $em->persist($message);
            $em->flush();
        }

        return $this->render('connected/salon.html.twig', [
            'usePlanet' => $usePlanet,
            'salons' => $salons,
            'formObject' => $form_message,
        ]);
    }
}