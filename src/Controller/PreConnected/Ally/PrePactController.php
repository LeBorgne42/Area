<?php

namespace App\Controller\PreConnected\Ally;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\AllyPeaceType;
use App\Entity\Pna;
use App\Entity\Allied;
use App\Entity\Salon;
use App\Entity\Peace;
use DateTime;
use DateTimeZone;
use Dateinterval;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class PrePactController extends AbstractController
{
    /**
     * @Route("/pre-accepter-pacte/{id}", name="pre_ally_acceptAllied", requirements={"id"="\d+"})
     */
    public function pactAcceptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
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
        $ally->addAllyAllied($allied);
        $salon = new Salon();
        $salon->setName($pact->getAlly()->getSigle() . " - " . $ally->getSigle());
        $salon->addAlly($pact->getAlly());
        $salon->addAlly($ally);
        $em->persist($salon);

        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-refuser-pacte/{id}", name="pre_ally_refuseAllied", requirements={"id"="\d+"})
     */
    public function pactRefuseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $pact = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-accepter-pna/{id}", name="pre_ally_acceptPna", requirements={"id"="\d+"})
     */
    public function pnaAcceptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
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
        $ally->addAllyPna($pna);

        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-refuser-pna/{id}", name="pre_ally_refusePna", requirements={"id"="\d+"})
     */
    public function pnaRefuseAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-detruire-pna/{id}", name="pre_ally_remove_pna", requirements={"id"="\d+"})
     */
    public function allyPnaRefuseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

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
            ->setParameters([
                'allytag' => $ally->getSigle(),
                'ally' => $otherAlly])
            ->getQuery()
            ->getOneOrNullResult();

        if($pact2) {
            $em->remove($pact2);
        }
        $em->remove($pact);
        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-detruire-pacte/{id}", name="pre_ally_remove_pact", requirements={"id"="\d+"})
     */
    public function allyPactRefuseAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 43200 . 'S'));

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
            ->setParameters([
                'allytag' => $user->getAlly()->getSigle(),
                'ally' => $otherAlly])
            ->getQuery()
            ->getOneOrNullResult();

        if($pact2) {
            $pact2->setDismissAt($now);
            $pact2->setDismissBy($user->getAlly()->getSigle());
            $pact->setDismissAt($now);
            $pact->setDismissBy($user->getAlly()->getSigle());
            $em->flush();
        } else {
            $em->remove($pact);
            $em->flush();
        }

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-faire-la-paix/{id}", name="pre_ally_make_peace", requirements={"id"="\d+"})
     */
    public function allyMakePeaceAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 864000 . 'S'));

        $war = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $waitingPeaces = $em->getRepository('App:Peace')
            ->createQueryBuilder('p')
            ->where('p.allyTag = :sigle')
            ->andWhere('p.accepted = false')
            ->setParameters(['sigle' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        $form_peace = $this->createForm(AllyPeaceType::class);
        $form_peace->handleRequest($request);

        if (($form_peace->isSubmitted() && $form_peace->isValid())) {
            $peace = new Peace();
            $peace->setAlly($ally);
            $peace->setAllyTag($war->getAllyTag());
            $peace->setSignedAt($now);
            $peace->setType($form_peace->get('type')->getData());
            $peace->setPlanet($form_peace->get('planetNbr')->getData());
            $peace->setTaxe($form_peace->get('taxeNbr')->getData());
            $peace->setPdg($form_peace->get('pdgNbr')->getData());
            $em->persist($peace);
            $em->flush();

            $form_peace->get('type')->getData();
        }

        return $this->render('preconnected/ally/makePeace.html.twig', [
            'form_peace' => $form_peace->createView(),
            'waitingPeaces' => $waitingPeaces,
        ]);
    }

    /**
     * @Route("/pre-accepter-paix/{id}", name="pre_ally_accept_peace", requirements={"id"="\d+"})
     */
    public function allyAcceptPeaceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 864000 . 'S'));

        $peace = $em->getRepository('App:Peace')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();


        $otherAlly = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.sigle = :sigle')
            ->setParameter('sigle', $peace->getAllyTag())
            ->getQuery()
            ->getOneOrNullResult();

        $war = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.ally = :ally')
            ->setParameter('ally', $ally)
            ->getQuery()
            ->getOneOrNullResult();

        $war2 = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.ally = :ally')
            ->setParameter('ally', $otherAlly)
            ->getQuery()
            ->getOneOrNullResult();

        if($peace->getType() == 0) {
            $type = 1;

        } else {
            $type = 0;

        }
        if($war && $war2) {
            $em->remove($war);
            $em->remove($war2);
        }
        $peace2 = new Peace();
        $peace2->setAlly($otherAlly);
        $peace2->setAllyTag($peace->getAlly()->getSigle());
        $peace2->setSignedAt($now);
        $peace2->setType($type);
        $peace2->setPlanet($peace->getPlanet());
        $peace2->setTaxe($peace->getTaxe());
        $peace2->setPdg($peace->getPdg());
        $peace->setSignedAt($now);
        $peace2->setAccepted(true);
        $peace->setAccepted(true);
        $em->persist($peace2);
        $em->flush();

        return $this->redirectToRoute('pre_ally_page_pacts');
    }

    /**
     * @Route("/pre-refuser-paix/{id}", name="pre_ally_remove_peace", requirements={"id"="\d+"})
     */
    public function allyRemovePeaceAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $peace = $em->getRepository('App:Peace')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();


        $otherAlly = $em->getRepository('App:Ally')
            ->createQueryBuilder('a')
            ->where('a.sigle = :sigle')
            ->setParameter('sigle', $peace->getAllyTag())
            ->getQuery()
            ->getOneOrNullResult();

        $peace2 = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.allyTag = :allytag')
            ->andWhere('al.ally = :ally')
            ->setParameters([
                'allytag' => $user->getAlly()->getSigle(),
                'ally' => $otherAlly])
            ->getQuery()
            ->getOneOrNullResult();

        if($peace2) {
            $em->remove($peace2);
            $em->remove($peace);
            $em->flush();
        } else {
            $em->remove($peace);
            $em->flush();
        }

        return $this->redirectToRoute('pre_ally_page_pacts');
    }
}