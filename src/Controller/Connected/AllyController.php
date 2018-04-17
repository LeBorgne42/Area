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

        if ($form_allyAdd->isSubmitted() && $form_allyAdd->isValid()) {
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
}