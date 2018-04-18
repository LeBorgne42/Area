<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Grade;
use App\Entity\Ally;
use App\Entity\Proposal;
use App\Entity\Pna;
use App\Entity\Allied;
use App\Entity\War;
use DateTime;

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
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
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
        $em->persist($ally);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/refuser-pacte/{id}", name="ally_refuseAllied", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pactRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
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
     * @Route("/accepter-pna/{id}", name="ally_acceptPna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $now = new DateTime();
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
     * @Route("/refuser-pna/{id}", name="ally_refusePna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function pnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
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
     * @Route("/detruire-pna/{id}", name="ally_remove_pna", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyPnaRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
        $pact = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

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

        $em->remove($pact);
        $em->remove($pact2);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }

    /**
     * @Route("/detruire-pacte/{id}", name="ally_remove_pact", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyPactRefuseAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $idp)
            ->getQuery()
            ->getOneOrNullResult();

        $user = $this->getUser();
        $ally = $user->getAlly();
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
                'allytag' => $ally->getSigle(),
                'ally' => $otherAlly))
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($pact);
        $em->remove($pact2);

        $em->flush();

        return $this->redirectToRoute('ally_page_pacts', array('idp' => $usePlanet->getId()));
    }
}