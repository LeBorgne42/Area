<?php

namespace App\Controller\Connected\Ally;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\AllyPeaceType;
use App\Entity\Pna;
use App\Entity\Allied;
use App\Entity\Salon;
use DateTime;
use DateTimeZone;
use Dateinterval;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class PactController extends Controller
{
    /**
     * @Route("/accepter-pacte/{id}/{idp}", name="ally_acceptAllied", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pactAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $pact = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $allied = new Allied();
        $allied->setAlly($ally);
        $allied->setAllyTag($pact->getAlly()->getSigle());
        $allied->setSignedAt($now);
        $allied->setAccepted(true);
        $pact->setAccepted(true);
        $em->persist($allied);
        $em->persist($pact);
        $ally->addAllyAllied($allied);
        $salon = new Salon();
        $salon->setName($pact->getAlly()->getSigle() . " - " . $ally->getSigle());
        $salon->addAlly($pact->getAlly());
        $salon->addAlly($ally);
        $em->persist($salon);
        $em->persist($ally);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/refuser-pacte/{id}/{idp}", name="ally_refuseAllied", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pactRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $pact = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/accepter-pna/{id}/{idp}", name="ally_acceptPna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $pna = new Pna();
        $pna->setAlly($ally);
        $pna->setAllyTag($pact->getAlly()->getSigle());
        $pna->setSignedAt($now);
        $pna->setAccepted(true);
        $pact->setAccepted(true);
        $em->persist($pna);
        $em->persist($pact);
        $ally->addAllyPna($pna);
        $em->persist($ally);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/refuser-pna/{id}/{idp}", name="ally_refusePna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-pna/{id}/{idp}", name="ally_remove_pna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyPnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $otherAlly = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.sigle = :sigle')
            ->setParameter('sigle', $pact->getAllyTag())
            ->getQuery()
            ->getOneOrNullResult();

        $pact2 = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.allyTag = :allytag')
            ->andWhere('pna.ally = :ally')
            ->setParameters(array(
                'allytag' => $ally->getSigle(),
                'ally' => $otherAlly))
            ->getQuery()
            ->getOneOrNullResult();

        if($pact2) {
            $em->remove($pact2);
        }
        $em->remove($pact);
        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-pacte/{id}/{idp}", name="ally_remove_pact", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyPactRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 43200 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $pact = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();


        $otherAlly = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.sigle = :sigle')
            ->setParameter('sigle', $pact->getAllyTag())
            ->getQuery()
            ->getOneOrNullResult();

        $pact2 = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.allyTag = :allytag')
            ->andWhere('al.ally = :ally')
            ->setParameters(array(
                'allytag' => $user->getAlly()->getSigle(),
                'ally' => $otherAlly))
            ->getQuery()
            ->getOneOrNullResult();

        $pact->setDismissAt($now);
        $pact->setDismissBy($user->getAlly()->getSigle());
        $pact2->setDismissAt($now);
        $pact2->setDismissBy($user->getAlly()->getSigle());
        $em->persist($pact2);
        $em->persist($pact);
        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/faire-la-paix/{id}/{idp}", name="ally_make_peace", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyMakePeaceAction(Request $request, $id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 43200 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $war = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $form_peace = $this->createForm(AllyPeaceType::class);
        $form_peace->handleRequest($request);

        if (($form_peace->isSubmitted() && $form_peace->isValid())) {

        }

        return $this->render('connected/ally/makePeace.html.twig', [
            'form_peace' => $form_peace->createView(),
            'usePlanet' => $usePlanet,
        ]);
    }
}