<?php

namespace App\Controller\PreConnected\Ally;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserAllyType;
use App\Form\Front\AllyImageType;
use App\Form\Front\AllyAddType;
use App\Form\Front\AllyPactType;
use App\Form\Front\AllyGradeType;
use App\Form\Front\ExchangeType;
use App\Form\Front\PdgType;
use App\Form\Front\UserAttrGradeType;
use App\Entity\Grade;
use App\Entity\Ally;
use App\Entity\Proposal;
use App\Entity\Exchange;
use App\Entity\Pna;
use App\Entity\Allied;
use App\Entity\War;
use DateTime;
use DateTimeZone;
use Dateinterval;
use App\Entity\Salon;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class PreAllyController extends Controller
{
    /**
     * @Route("/pre-alliance", name="pre_ally")
     */
    public function preAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class, $ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->flush();
        }
        $allAllys = $em->getRepository('App:Ally')->findAll();

        return $this->render('preconnected/ally.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
            'allAllys' => $allAllys
        ]);
    }

    /**
     * @Route("/pre-attribution-grade/{id}", name="pre_ally_addUser_grade")
     */
    public function preAllyAddUserGradeAction(Request $request, $id)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        $form_userAttrGrade = $this->createForm(UserAttrGradeType::class, null, ["allyId" => $user->getAlly()->getId()]);
        $form_userAttrGrade->handleRequest($request);


        if (($form_userAttrGrade->isSubmitted() && $form_userAttrGrade->isValid())) {
            $newGradeUser = $em->getRepository('App:User')->find(['id' => $id]);

            if(($user->getGrade()->getPlacement() == 1 && $newGradeUser->getId() == $user->getId()) && $form_userAttrGrade->get('grade')->getData()->getPlacement() != 1) {
                return $this->redirectToRoute('pre_ally');
            }
            $newGradeUser->setGrade($form_userAttrGrade->get('grade')->getData());
            $em->flush();

            return $this->redirectToRoute('pre_ally');
        }

        return $this->render('preconnected/ally/grade.html.twig', [
            'form_userAttrGrade' => $form_userAttrGrade->createView(),
            'idUser' => $id,
        ]);
    }

    /**
     * @Route("/pre-cherche-alliance", name="pre_ally_blank")
     */
    public function preNoAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            return $this->redirectToRoute('pre_ally');
        } else {
            $ally = new Ally();
        }

        $form_ally = $this->createForm(UserAllyType::class, $ally);
        $form_ally->handleRequest($request);

        if ($form_ally->isSubmitted() && $form_ally->isValid()) {
            if($user->getAllyBan() > $now) {
                return $this->redirectToRoute('pre_ally_blank');
            }
            $grade = new Grade();

            $ally->addUser($user);
            $ally->setBitcoin(5000);
            $ally->setPdg(50);
            $ally->setCreatedAt($now);
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
            $mGrade = new Grade();
            $mGrade->setAlly($ally);
            $mGrade->setName("Membre");
            $mGrade->addUser($user);
            $mGrade->setPlacement(5);
            $em->persist($mGrade);

            $ally->addGrade($mGrade);


            $salon = new Salon();
            $salon->setName($ally->getName());
            $salon->addAlly($ally);
            $em->persist($salon);

            $salonPublic = new Salon();
            $salonPublic->setName('Ambassade - ' . $ally->getSigle());
            $salonPublic->addAlly($ally);
            $em->persist($salonPublic);

            $ally->addGrade($grade);
            $user->setAlly($ally);
            $user->setJoinAllyAt($now);
            $user->setGrade($grade);
            $em->persist($ally);
            $em->flush();

            return $this->redirectToRoute('pre_ally');
        }
        $allAllys = $em->getRepository('App:Ally')->findAll();

        return $this->render('preconnected/ally/noAlly.html.twig', [
            'form_ally' => $form_ally->createView(),
            'allAllys' => $allAllys
        ]);
    }

    /**
     * @Route("/pre-supprimer-alliance", name="pre_remove_ally")
     */
    public function preRemoveAllyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();
        $ally = $user->getAlly();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        foreach ($ally->getUsers() as $user) {
            $user->setAlly(null);
            $user->setGrade(null);
            $user->setAllyBan($now);
        }
        foreach ($ally->getGrades() as $grade) {
            $em->remove($grade);
        }
        foreach ($ally->getSalons() as $salon) {
            foreach ($salon->getContents() as $content) {
                $em->remove($content);
            }
            $em->remove($salon);
        }
        foreach ($ally->getExchanges() as $exchange) {
            $em->remove($exchange);
        }

        foreach ($ally->getPnas() as $pna) {
            $em->remove($pna);
        }

        foreach ($ally->getWars() as $war) {
            $em->remove($war);
        }

        foreach ($ally->getAllieds() as $allied) {
            $em->remove($allied);
        }

        foreach ($ally->getProposals() as $proposal) {
            $em->remove($proposal);
        }
        $em->flush();

        $pnas = $em->getRepository('App:Pna')
            ->createQueryBuilder('p')
            ->where('p.allyTag = :allytag')
            ->setParameters(['allytag' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        $pacts = $em->getRepository('App:Allied')
            ->createQueryBuilder('a')
            ->where('a.allyTag = :allytag')
            ->setParameters(['allytag' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        $wars = $em->getRepository('App:War')
            ->createQueryBuilder('w')
            ->where('w.allyTag = :allytag')
            ->setParameters(['allytag' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        foreach ($pnas as $pna) {
            $em->remove($pna);
        }

        foreach ($pacts as $pact) {
            $em->remove($pact);
        }

        foreach ($wars as $war) {
            $em->remove($war);
        }

        $em->remove($ally);
        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-quitter-alliance", name="pre_leave_ally")
     */
    public function preLeaveAllyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getGrade()->getPlacement() == 1 || count($user->getAlly()->getUsers()) == 1) {
            return $this->redirectToRoute('pre_ally');
        }
        $user->setAlly(null);
        $user->setJoinAllyAt(null);
        $user->setGrade(null);
        $user->setAllyBan($now);

        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-exclusion-alliance/{id}", name="pre_ally_kick")
     */
    public function preKickAllyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getGrade()->getCanKick() == 0) {
            return $this->redirectToRoute('pre_ally');
        }
        $kicked = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->join('u.grade', 'g')
            ->where('u.id = :id')
            ->andWhere('g.placement > :nbr')
            ->setParameters([
                'id' => $id,
                'nbr' => 1])
            ->getQuery()
            ->getOneOrNullResult();

        if(!$kicked) {
            return $this->redirectToRoute('pre_ally');
        }
        $ally = $user->getAlly();
        $ally->removeUser($kicked);
        $kicked->setAlly(null);
        $kicked->setJoinAllyAt(null);
        $kicked->setGrade(null);
        $kicked->setAllyBan($now);

        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-rejoindre-alliance/{id}", name="pre_ally_accept")
     */
    public function preAllyAcceptAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            return $this->redirectToRoute('pre_ally');
        }
        if($user->getAllyBan() > $now) {
            return $this->redirectToRoute('pre_ally_blank');
        }
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);


        $ally = $proposal->getAlly();
        $ally->addUser($user);
        $user->setAlly($ally);
        $user->setJoinAllyAt($now);
        $user->setGrade($ally->getNewMember());
        $em->remove($proposal);

        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-refuser-alliance/{id}", name="pre_ally_refuse")
     */
    public function preAllyRefusetAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-annuler-alliance/{id}", name="pre_ally_cancel")
     */
    public function preAllyCanceltAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('pre_ally');
    }

    /**
     * @Route("/pre-bye-bye-les-losers", name="pre_ally_page_exit")
     */
    public function preExitPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('pre_ally');
        }

        return $this->render('preconnected/ally/exit.html.twig', [
            'form_allyImage' => $form_allyImage->createView()
        ]);
    }

    /**
     * @Route("/prereserve-commune", name="pre_ally_page_bank")
     */
    public function preBankPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }
        $exchanges = $em->getRepository('App:Exchange')
            ->createQueryBuilder('e')
            ->andWhere('e.ally = :ally')
            ->setParameters(['ally' => $user->getAlly()])
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('preconnected/ally/bank.html.twig', [
            'exchanges' => $exchanges,
        ]);
    }

    /**
     * @Route("/pre-ajouter-des-membres", name="pre_ally_page_add")
     */
    public function preAddPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();
        $maxMembers = count($user->getAlly()->getUsers()) + count($user->getAlly()->getProposals());
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }

        $form_allyAdd = $this->createForm(AllyAddType::class);
        $form_allyAdd->handleRequest($request);

        if (($form_allyAdd->isSubmitted() && $form_allyAdd->isValid()) && $user->getGrade()->getCanRecruit() == 1) {
            if($maxMembers >= 6) {
                return $this->redirectToRoute('pre_ally_blank');
            }
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
                $em->flush();
            }
            return $this->redirectToRoute('pre_ally_page_add');
        }

        $lastActivity = new DateTime();
        $lastActivity->setTimezone(new DateTimeZone('Europe/Paris'));
        $lastActivity->sub(new DateInterval('PT' . 5184000 . 'S'));
        $usersRecruitable = $em->getRepository('App:User')
            ->createQueryBuilder('u')
            ->where('u.lastActivity > :date')
            ->andWhere('u.ally is null')
            ->setParameters(['date' => $lastActivity])
            ->orderBy('u.lastActivity', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('preconnected/ally/add.html.twig', [
            'form_allyAdd' => $form_allyAdd->createView(),
            'usersRecrutable' => $usersRecruitable,
        ]);
    }

    /**
     * @Route("/pre-administration-alliance", name="pre_ally_page_admin")
     */
    public function preAdminPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        $grade = new Grade();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }

        $form_allyGrade = $this->createForm(AllyGradeType::class,$grade);
        $form_allyGrade->handleRequest($request);

        if (($form_allyGrade->isSubmitted() && $form_allyGrade->isValid())) {
            $grade->setAlly($ally);
            $em->persist($grade);
            $ally->addGrade($grade);
            $em->flush();

            return $this->redirectToRoute('pre_ally_page_admin');
        }

        return $this->render('preconnected/ally/admin.html.twig', [
            'form_allyGrade' => $form_allyGrade->createView(),
        ]);
    }

    /**
     * @Route("/pre-ambassade-interne", name="pre_ally_page_pacts")
     */
    public function prePactPageAllyAction(Request $request)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();
        $server = $em->getRepository('App:Server')->find(['id' => 1]);
        if($server->getOpen() == true) {
            return $this->redirectToRoute('login_redirect');
        }

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('pre_ally_blank');
        }
        $waitingPna = $em->getRepository('App:Pna')
            ->createQueryBuilder('pna')
            ->where('pna.allyTag = :sigle')
            ->andWhere('pna.accepted = false')
            ->setParameters(['sigle' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        $waitingAllied = $em->getRepository('App:Allied')
            ->createQueryBuilder('al')
            ->where('al.allyTag = :sigle')
            ->andWhere('al.accepted = false')
            ->setParameters(['sigle' => $ally->getSigle()])
            ->getQuery()
            ->getResult();

        $form_allyPact = $this->createForm(AllyPactType::class);
        $form_allyPact->handleRequest($request);


        if (($form_allyPact->isSubmitted() && $form_allyPact->isValid())) {
            $allyPact = $em->getRepository('App:Ally')
                ->createQueryBuilder('a')
                ->where('a.sigle = :sigle')
                ->setParameter('sigle', $form_allyPact->get('allyName')->getData())
                ->getQuery()
                ->getOneOrNullResult();

            if((!$allyPact || $user->getAlly()->getAlreadyPact($allyPact->getSigle())) || $allyPact == $ally) {
                return $this->redirectToRoute('pre_ally_page_pacts');
            }
            if($form_allyPact->get('pactType')->getData() == 2 && $user->getGrade()->getCanPeace() == 1) {
                $pna = new Pna();
                $pna->setAlly($ally);
                $pna->setAllyTag($allyPact->getSigle());
                $pna->setSignedAt($now);
                $em->persist($pna);
                $ally->addAllyPna($pna);
            } elseif($form_allyPact->get('pactType')->getData() == 1  && $user->getGrade()->getCanPeace() == 1) {
                $allied = new Allied();
                $allied->setAlly($ally);
                $allied->setAllyTag($allyPact->getSigle());
                $allied->setSignedAt($now);
                $em->persist($allied);
                $ally->addAllyAllied($allied);
            } elseif($form_allyPact->get('pactType')->getData() == 3 && $user->getGrade()->getCanWar() == 1) {
                $salon = new Salon();
                $salon->setName("Guerre : " . $allyPact->getSigle() . " - " . $ally->getSigle());
                $salon->addAlly($allyPact);
                $salon->addAlly($ally);
                $em->persist($salon);
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
            }
            $em->flush();
            return $this->redirectToRoute('pre_ally_page_pacts');
        }

        $allAllys = $em->getRepository('App:Ally')->findAll();

        return $this->render('preconnected/ally/pact.html.twig', [
            'waitingPna' => $waitingPna,
            'waitingAllied' => $waitingAllied,
            'form_allyPact' => $form_allyPact->createView(),
            'allAllys' => $allAllys
        ]);
    }
}