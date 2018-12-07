<?php

namespace App\Controller\Connected\Ally;

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
class PactController extends AbstractController
{
    /**
     * @Route("/accepter-pacte/{id}/{idp}", name="ally_acceptAllied", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pactAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/refuser-pacte/{id}/{idp}", name="ally_refuseAllied", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pactRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $pact = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/accepter-pna/{id}/{idp}", name="ally_acceptPna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/refuser-pna/{id}/{idp}", name="ally_refusePna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/detruire-pna/{id}/{idp}", name="ally_remove_pna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyPnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
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

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/faire-la-paix/{id}/{idp}", name="ally_make_peace", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyMakePeaceAction(Request $request, $id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 864000 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->render('connected/ally/makePeace.html.twig', [
            'form_peace' => $form_peace->createView(),
            'usePlanet' => $usePlanet,
            'waitingPeaces' => $waitingPeaces,
        ]);
    }

    /**
     * @Route("/accepter-paix/{id}/{idp}", name="ally_accept_peace", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyAcceptPeaceAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 864000 . 'S'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/refuser-paix/{id}/{idp}", name="ally_remove_peace", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyRemovePeaceAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
    }
}