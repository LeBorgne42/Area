<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserAllyType;
use App\Form\Front\AllyImageType;
use App\Form\Front\AllyAddType;
use App\Form\Front\AllyPactType;
use App\Form\Front\AllyGradeType;
use App\Form\Front\UserAttrGradeType;
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
class AllyController extends Controller
{
    /**
     * @Route("/alliance", name="ally")
     * @Route("/alliance/", name="ally_withSlash")
     */
    public function allyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return $this->render('connected/ally.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/attribution-grade/{id}", name="ally_addUser_grade", requirements={"id"="\d+"})
     */
    public function allyAddUserGradeAction(Request $request, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $form_userAttrGrade = $this->createForm(UserAttrGradeType::class, null, array("allyId" => $user->getAlly()->getId()));
        $form_userAttrGrade->handleRequest($request);

        if (($form_userAttrGrade->isSubmitted() && $form_userAttrGrade->isValid())) {
            $newGradeUser = $em->getRepository('App:User')
                ->createQueryBuilder('u')
                ->where('u.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->getOneOrNullResult();

            if(($user->getGrade()->getPlacement() == 1 && $newGradeUser->getId() == $user->getId()) && $form_userAttrGrade->get('grade')->getData()->getPlacement() != 1) {
                return $this->redirectToRoute('ally');
            }
            $newGradeUser->setGrade($form_userAttrGrade->get('grade')->getData());
            $em->persist($newGradeUser);
            $em->flush();

            return $this->redirectToRoute('ally');
        }

        return $this->render('connected/ally/grade.html.twig', [
            'form_userAttrGrade' => $form_userAttrGrade->createView(),
            'idUser' => $id,
        ]);
    }

    /**
     * @Route("/cherche-alliance", name="ally_blank")
     * @Route("/cherche-alliance/", name="ally_blank_withSlash")
     */
    public function noAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            return $this->redirectToRoute('ally');
        } else {
            $ally = new Ally();
        }

        $form_ally = $this->createForm(UserAllyType::class, $ally);
        $form_ally->handleRequest($request);

        if ($form_ally->isSubmitted() && $form_ally->isValid()) {
            $grade = new Grade();

            $ally->addUser($user);
            $ally->setBitcoin(200);
            $em->persist($ally);

            $grade->setAlly($ally);
            $grade->setName("Dirigeant");
            $grade->addUser($user);
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

            return $this->redirectToRoute('ally');
        }
        return $this->render('connected/ally/noAlly.html.twig', [
            'form_ally' => $form_ally->createView(),
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

        $pnas = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.allyTag = :allytag')
            ->setParameter('allytag', $ally->getSigle())
            ->getQuery()
            ->getResult();

        $pacts = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.allyTag = :allytag')
            ->setParameter('allytag', $ally->getSigle())
            ->getQuery()
            ->getResult();

        $wars = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.allyTag = :allytag')
            ->setParameter('allytag', $ally->getSigle())
            ->getQuery()
            ->getResult();

        foreach ($pnas as $pna) {
            $em->remove($pna);
            $em->flush();
        }

        foreach ($pacts as $pact) {
            $em->remove($pact);
            $em->flush();
        }

        foreach ($wars as $war) {
            $em->remove($war);
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
        if($user->getGrade()->getPlacement() == 1 || count($user->getAlly()->getUsers()) == 1) {
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
     * @Route("/exclusion-alliance/{id}", name="ally_kick", requirements={"id"="\d+"})
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
            ->join('u.grade', 'g')
            ->where('u.id = :id')
            ->andWhere('g.placement > :nbr')
            ->setParameters(array(
                'id' => $id,
                'nbr' => 1))
            ->getQuery()
            ->getOneOrNullResult();

        if(!$kicked) {
            return $this->redirectToRoute('ally');
        }
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
        $grade->setName("Membre");
        $grade->addUser($user);
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

    /**
     * @Route("/bye-bye-les-losers", name="ally_page_exit")
     * @Route("/bye-bye-les-losers/", name="ally_page_exit_withSlash")
     */
    public function exitPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('ally');
        }

        return $this->render('connected/ally/exit.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/reserve-commune", name="ally_page_bank")
     * @Route("/reserve-commune/", name="ally_page_bank_withSlash")
     */
    public function bankPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return $this->render('connected/ally/bank.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/ajouter-des-membres", name="ally_page_add")
     * @Route("/ajouter-des-membres/", name="ally_page_add_withSlash")
     */
    public function addPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        $form_allyAdd = $this->createForm(AllyAddType::class);
        $form_allyAdd->handleRequest($request);

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
            return $this->redirectToRoute('ally_page_add');
        }

        return $this->render('connected/ally/add.html.twig', [
            'form_allyAdd' => $form_allyAdd->createView(),
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/administration-alliance", name="ally_page_admin")
     * @Route("/administration-alliance/", name="ally_page_admin_withSlash")
     */
    public function adminPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $grade = new Grade();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        $form_allyGrade = $this->createForm(AllyGradeType::class,$grade);
        $form_allyGrade->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        if (($form_allyGrade->isSubmitted() && $form_allyGrade->isValid())) {
            $grade->setAlly($ally);
            $em->persist($grade);
            $ally->addGrade($grade);
            $em->persist($ally);
            $em->flush();

            return $this->redirectToRoute('ally_page_admin');
        }

        return $this->render('connected/ally/admin.html.twig', [
            'form_allyGrade' => $form_allyGrade->createView(),
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }

    /**
     * @Route("/ambassade-interne", name="ally_page_pacts")
     * @Route("/ambassade-interne/", name="ally_page_pacts_withSlash")
     */
    public function pactPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $em = $this->getDoctrine()->getManager();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank');
        }
        $waitingPna = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.allyTag = :sigle')
            ->andWhere('pna.accepted = :false')
            ->setParameters(array(
                'sigle' => $ally->getSigle(),
                'false' => false))
            ->getQuery()
            ->getResult();
        $waitingAllied = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.allyTag = :sigle')
            ->andWhere('al.accepted = :false')
            ->setParameters(array(
                'sigle' => $ally->getSigle(),
                'false' => false))
            ->getQuery()
            ->getResult();

        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        $form_allyPact = $this->createForm(AllyPactType::class);
        $form_allyPact->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
        }


        if (($form_allyPact->isSubmitted() && $form_allyPact->isValid())) {
            $allyPact = $em->getRepository('App:Ally')
                ->createQueryBuilder('a')
                ->where('a.sigle = :sigle')
                ->setParameter('sigle', $form_allyPact->get('allyName')->getData())
                ->getQuery()
                ->getOneOrNullResult();

            if(!$allyPact) {
                return $this->redirectToRoute('ally');
            }
            if($form_allyPact->get('pactType')->getData() == 1 && $user->getGrade()->getCanPeace() == 1) {
                $pna = new Pna();
                $pna->setAlly($ally);
                $pna->setAllyTag($allyPact->getSigle());
                $pna->setSignedAt($now);
                $em->persist($pna);
                $ally->addAllyPna($pna);
            } elseif($form_allyPact->get('pactType')->getData() == 2 && $user->getGrade()->getCanPeace() == 1) {
                $allied = new Allied();
                $allied->setAlly($ally);
                $allied->setAllyTag($allyPact->getSigle());
                $allied->setSignedAt($now);
                $em->persist($allied);
                $ally->addAllyAllied($allied);
            } elseif($form_allyPact->get('pactType')->getData() == 3 && $user->getGrade()->getCanWar() == 1) {
                $war = new War();
                $war2 = new War();
                $war2->setAlly($allyPact);
                $war2->setAllyTag($ally->getSigle());
                $war2->setSignedAt($now);
                $war2->setAccepted(true);
                $war->setAccepted(true);
                $war->setAlly($ally);
                $war->setAllyTag($allyPact->getSigle());
                $war->setSignedAt($now);
                $em->persist($war);
                $em->persist($war2);
                $ally->addAllyWar($war);
                $allyPact->addAllyWar($war2);
                $em->persist($allyPact);
            }
            $em->persist($ally);
            $em->flush();
            return $this->redirectToRoute('ally_page_pacts');
        }

        return $this->render('connected/ally/pact.html.twig', [
            'waitingPna' => $waitingPna,
            'waitingAllied' => $waitingAllied,
            'form_allyPact' => $form_allyPact->createView(),
            'form_allyImage' => $form_allyImage->createView(),
        ]);
    }
}