<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserAllyType;
use App\Form\Front\AllyImageType;
use App\Form\Front\AllyAddType;
use App\Entity\Grade;
use App\Entity\Ally;
use App\Entity\Proposal;
use DateTime;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class AllyController extends Controller
{
    /**
     * @Route("/alliance", name="ally")
     * @Route("/alliance/", name="ally_withSlash")
     */
    public function allyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            $ally = new Ally();
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        $form_allyAdd = $this->createForm(AllyAddType::class);
        $form_allyAdd->handleRequest($request);

        $form_ally = $this->createForm(UserAllyType::class, $ally);
        $form_ally->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        if (($form_allyAdd->isSubmitted() && $form_allyAdd->isValid()) && $user->getGrade()->getCanRecruit() == 1) {
            $userProposal = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.username = :username')
                ->andWhere('u.ally is null')
                ->setParameter('username', $form_allyAdd->get('nameUser')->getData())
                ->getQuery()
                ->getOneOrNullResult();

            if($userProposal) {
                $proposal = new Proposal();
                $proposal->setUser($userProposal);
                $proposal->setAlly($ally);
                $proposal->setProposalAt($now);
                $em->persist($proposal);
                $ally->addProposal($proposal);
                $userProposal->addProposal($proposal);
                $em->persist($ally);
                $em->persist($userProposal);
                $em->flush();
            }
            return $this->redirectToRoute('ally');
        }

        if($this->getUser()->getAlly()) {
            return $this->render('connected/ally.html.twig', [
                'form_ally' => $form_ally->createView(),
                'form_allyAdd' => $form_allyAdd->createView(),
                'form_allyImage' => $form_allyImage->createView(),
            ]);
        }

        if ($form_ally->isSubmitted() && $form_ally->isValid()) {
            $grade = new Grade();

            $ally->addUser($user);
            $ally->setBitcoin(200);
            $em->persist($ally);

            $grade->setAlly($ally);
            $grade->setName("Dirigeant");
            $grade->setUser($user);
            $grade->setPlacement(1);
            $grade->setCanRecruit(true);
            $grade->setCanKick(true);
            $grade->setCanWar(true);
            $grade->setCanPeace(true);
            $em->persist($grade);

            $ally->addGrade($grade);
            $user->setAlly($ally);
            $user->setJoinAllyAt($now);
            $user->setGrade($grade);
            $em->persist($user);
            $em->persist($ally);
            $em->flush();
        }
        return $this->render('connected/ally.html.twig', [
            'form_ally' => $form_ally->createView(),
            'form_allyAdd' => $form_allyAdd->createView(),
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/supprimer-alliance", name="remove_ally")
     * @Route("/supprimer-alliance/", name="remove_ally_withSlash")
     */
    public function removeAllyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ally = $user->getAlly();
        $user->setAlly(null);
        $user->setGrade(null);
        $em->persist($user);

        foreach ($ally->getGrades() as $grade) {
            $em->remove($grade);
            $em->flush();
        }

        $em->remove($ally);
        $em->flush();

        return $this->redirectToRoute('ally');
    }

    /**
     * @Route("/quitter-alliance", name="leave_ally")
     * @Route("/quitter-alliance/", name="leave_ally_withSlash")
     */
    public function leaveAllyAction()
    {
        $user = $this->getUser();
        if($user->getGrade()->getPlacement() == 1) {
            return $this->redirectToRoute('ally');
        }
        $em = $this->getDoctrine()->getManager();
        $user->setAlly(null);
        $user->setJoinAllyAt(null);
        $user->setGrade(null);
        $em->persist($user);

        $em->flush();

        return $this->redirectToRoute('ally');
    }

    /**
     * @Route("/quitter-alliance/{id}", name="ally_kick", requirements={"id"="\d+"})
     */
    public function kickAllyAction($id)
    {
        $user = $this->getUser();
        if($user->getGrade()->getCanKick() == 0) {
            return $this->redirectToRoute('ally');
        }
        $em = $this->getDoctrine()->getManager();
        $kicked = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $ally = $user->getAlly();
        $ally->removeUser($kicked);
        $kicked->setAlly(null);
        $kicked->setJoinAllyAt(null);
        $kicked->setGrade(null);
        $em->persist($kicked);
        $em->persist($ally);

        $em->flush();

        return $this->redirectToRoute('ally');
    }

    /**
     * @Route("/rejoindre-alliance/{id}", name="ally_accept", requirements={"id"="\d+"})
     */
    public function allyAcceptAction($id)
    {
        $user = $this->getUser();
        if($user->getAlly()) {
            return $this->redirectToRoute('ally');
        }

        $em = $this->getDoctrine()->getManager();
        $proposal = $em->getRepository('App:Proposal')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();


        $ally = $proposal->getAlly();
        $ally->addUser($user);
        $em->persist($ally);

        $grade = new Grade();
        $now = new DateTime();
        $grade->setAlly($ally);
        $grade->setName("Nouveau");
        $grade->setUser($user);
        $grade->setPlacement(5);
        $em->persist($grade);

        $ally->addGrade($grade);
        $user->setAlly($ally);
        $user->setJoinAllyAt($now);
        $user->setGrade($grade);
        $em->remove($proposal);

        $em->persist($ally);
        $em->persist($user);
        $em->flush();

        return $this->redirectToRoute('ally');
    }

    /**
     * @Route("/refuser-alliance/{id}", name="ally_refuse", requirements={"id"="\d+"})
     */
    public function allyRefusetAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $proposal = $em->getRepository('App:Proposal')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('ally');
    }

    /**
     * @Route("/annuler-alliance/{id}", name="ally_cancel", requirements={"id"="\d+"})
     */
    public function allyCanceltAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $proposal = $em->getRepository('App:Proposal')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('ally');
    }
}