<?php

namespace App\Controller\Connected\Ally;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserAllyType;
use App\Form\Front\AllyImageType;
use App\Form\Front\AllyAddType;
use App\Form\Front\AllyPactType;
use App\Form\Front\AllyGradeType;
use App\Form\Front\AllyDefconType;
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
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class AllyController extends AbstractController
{
    /**
     * @Route("/alliance/{idp}", name="ally", requirements={"idp"="\d+"})
     */
    public function allyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }
        $form_allyImage = $this->createForm(AllyImageType::class, $ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->flush();
        }

        return $this->render('connected/ally.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
            'usePlanet' => $usePlanet
        ]);
    }

    /**
     * @Route("/attribution-grade/{id}/{idp}", name="ally_addUser_grade", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyAddUserGradeAction(Request $request, $id, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_userAttrGrade = $this->createForm(UserAttrGradeType::class, null, ["allyId" => $user->getAlly()->getId()]);
        $form_userAttrGrade->handleRequest($request);


        if (($form_userAttrGrade->isSubmitted() && $form_userAttrGrade->isValid())) {
            $newGradeUser = $em->getRepository('App:User')->find(['id' => $id]);

            if(($user->getGrade()->getPlacement() == 1 && $newGradeUser->getId() == $user->getId()) && $form_userAttrGrade->get('grade')->getData()->getPlacement() != 1) {
                return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
            }
            $newGradeUser->setGrade($form_userAttrGrade->get('grade')->getData());
            $em->flush();

            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/ally/grade.html.twig', [
            'form_userAttrGrade' => $form_userAttrGrade->createView(),
            'usePlanet' => $usePlanet,
            'idUser' => $id,
        ]);
    }

    /**
     * @Route("/cherche-alliance/{idp}", name="ally_blank", requirements={"idp"="\d+"})
     */
    public function noAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        } else {
            $ally = new Ally();
        }

        $form_ally = $this->createForm(UserAllyType::class, $ally);
        $form_ally->handleRequest($request);

        if ($form_ally->isSubmitted() && $form_ally->isValid()) {
            if($user->getAllyBan() > $now) {
                return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
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
            $quest = $user->checkQuests('ally_join');
            if($quest) {
                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
                $user->removeQuest($quest);
            }
            $em->flush();

            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }
        return $this->render('connected/ally/noAlly.html.twig', [
            'form_ally' => $form_ally->createView(),
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/supprimer-alliance/{idp}", name="remove_ally", requirements={"idp"="\d+"})
     */
    public function removeAllyAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();
        $ally = $user->getAlly();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

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

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/quitter-alliance/{idp}", name="leave_ally", requirements={"idp"="\d+"})
     */
    public function leaveAllyAction($idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGrade()->getPlacement() == 1 || count($user->getAlly()->getUsers()) == 1) {
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }
        $user->setAlly(null);
        $user->setJoinAllyAt(null);
        $user->setGrade(null);
        $user->setAllyBan($now);

        $em->flush();

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/exclusion-alliance/{id}/{idp}", name="ally_kick", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function kickAllyAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $now->add(new DateInterval('PT' . 172800 . 'S'));
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getGrade()->getCanKick() == 0) {
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
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
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }
        $ally = $user->getAlly();
        $ally->removeUser($kicked);
        $kicked->setAlly(null);
        $kicked->setJoinAllyAt(null);
        $kicked->setGrade(null);
        $kicked->setAllyBan($now);

        $em->flush();

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/rejoindre-alliance/{id}/{idp}", name="ally_accept", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyAcceptAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }
        if($user->getAllyBan() > $now) {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);


        $ally = $proposal->getAlly();
        $ally->addUser($user);
        $user->setAlly($ally);
        $user->setJoinAllyAt($now);
        $user->setGrade($ally->getNewMember());
        $em->remove($proposal);
        $quest = $user->checkQuests('ally_join');
        if($quest) {
            $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + $quest->getGain());
            $user->removeQuest($quest);
        }

        $em->flush();

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/refuser-alliance/{id}/{idp}", name="ally_refuse", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyRefusetAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/annuler-alliance/{id}/{idp}", name="ally_cancel", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyCanceltAction($id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $proposal = $em->getRepository('App:Proposal')->find(['id' => $id]);

        $em->remove($proposal);
        $em->flush();

        return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/bye-bye-les-losers/{idp}", name="ally_page_exit", requirements={"idp"="\d+"})
     */
    public function exitPageAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }
        $form_allyImage = $this->createForm(AllyImageType::class,$ally);
        $form_allyImage->handleRequest($request);

        if ($form_allyImage->isSubmitted() && $form_allyImage->isValid()) {
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('ally', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/ally/exit.html.twig', [
            'form_allyImage' => $form_allyImage->createView(),
            'usePlanet' => $usePlanet,
        ]);
    }

    /**
     * @Route("/reserve-commune/{idp}", name="ally_page_bank", requirements={"idp"="\d+"})
     */
    public function bankPageAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }
        $exchanges = $em->getRepository('App:Exchange')
            ->createQueryBuilder('e')
            ->andWhere('e.ally = :ally')
            ->setParameters(['ally' => $user->getAlly()])
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        $form_exchange = $this->createForm(ExchangeType::class);
        $form_exchange->handleRequest($request);

        if ($form_exchange->isSubmitted() && $form_exchange->isValid()) {
            $amountExchange = abs($form_exchange->get('amount')->getData());
            if ($form_exchange->get('valueType')->getData() == 1) {
                if ($form_exchange->get('exchangeType')->getData() == 1) {
                    if ($amountExchange <= $user->getBitcoin()) {
                        $user->setBitcoin($user->getBitcoin() - $amountExchange);
                        $ally->setBitcoin($ally->getBitcoin() + $amountExchange);
                        $exchange = new Exchange();
                        $exchange->setAlly($ally);
                        $exchange->setCreatedAt($now);
                        $exchange->setType(0);
                        $exchange->setAccepted(1);
                        $exchange->setContent($form_exchange->get('content')->getData());
                        $exchange->setAmount($amountExchange);
                        $exchange->setName($user->getUserName());
                        $em->persist($exchange);
                    }
                } else {
                    if ($amountExchange <= $ally->getBitcoin() && $user->getGrade()->getPlacement() == 1) {
                        $exchange = new Exchange();
                        $exchange->setAlly($ally);
                        $exchange->setCreatedAt($now);
                        $exchange->setType(0);
                        if ($user->getGrade()->getPlacement() == 1) {
                            $exchange->setAccepted(1);
                            $user->setBitcoin($user->getBitcoin() + $amountExchange);
                            $ally->setBitcoin($ally->getBitcoin() - $amountExchange);
                        } else {
                            $exchange->setAccepted(0);
                        }
                        $exchange->setContent($form_exchange->get('content')->getData());
                        $exchange->setAmount(-$amountExchange);
                        $exchange->setName($user->getUserName());
                        $em->persist($exchange);
                    }
                }
            } else {
                if($form_exchange->get('exchangeType')->getData() == 1) {
                    if($amountExchange <= $user->getRank()->getWarPoint()) {
                        $user->getRank()->setWarPoint(($user->getRank()->getWarPoint() - $amountExchange));
                        $ally->setPdg($ally->getPdg() + $amountExchange);
                        $exchange = new Exchange();
                        $exchange->setAlly($ally);
                        $exchange->setType(1);
                        $exchange->setAccepted(1);
                        $exchange->setContent($form_exchange->get('content')->getData());
                        $exchange->setCreatedAt($now);
                        $exchange->setAmount($amountExchange);
                        $exchange->setName($user->getUserName());
                        $em->persist($exchange);
                    }
                } else {
                    if($amountExchange <= $ally->getPdg() && $user->getGrade()->getPlacement() == 1) {
                        $exchange = new Exchange();
                        $exchange->setAlly($ally);
                        $exchange->setType(1);
                        if ($user->getGrade()->getPlacement() == 1) {
                            $exchange->setAccepted(1);
                            $user->getRank()->setWarPoint(($user->getRank()->getWarPoint() + $amountExchange));
                            $ally->setPdg($ally->getPdg() - $amountExchange);
                        } else {
                            $exchange->setAccepted(0);
                        }
                        $exchange->setContent($form_exchange->get('content')->getData());
                        $exchange->setCreatedAt($now);
                        $exchange->setAmount(-$amountExchange);
                        $exchange->setName($user->getUserName());
                        $em->persist($exchange);
                    }
                }
            }
            $form_exchange = null;
            $form_exchange = $this->createForm(ExchangeType::class);
            $em->flush();
        }

        return $this->render('connected/ally/bank.html.twig', [
            'form_exchange' => $form_exchange->createView(),
            'usePlanet' => $usePlanet,
            'exchanges' => $exchanges,
        ]);
    }

    /**
     * @Route("/accepter-echange/{id}/{idp}", name="ally_accept_exchange", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyAcceptExchangeAction(Exchange $id, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
        $userExchange = $em->getRepository('App:User')->findOneByUsername($id->getName());

        if($user->getAlly() && $user->getGrade()->getPlacement() == 1) {
            $amountExchange = abs($id->getAmount());
            if ($id->getType() == 0 && $amountExchange <= $id->getAlly()->getBitcoin()) {
                $userExchange->setBitcoin($user->getBitcoin() + $id->getAmount());
                $id->getAlly()->setBitcoin($id->getAlly()->getBitcoin() - $amountExchange);
                $id->setAccepted(1);
                $em->flush();
            } elseif ($amountExchange <= $id->getAlly()->getPdg()) {
                $userExchange->getRank()->setWarPoint(($userExchange->getRank()->getWarPoint() + $id->getAmount()));
                $id->getAlly()->setPdg($id->getAlly()->getPdg() - $amountExchange);
                $id->setAccepted(1);
                $em->flush();
            }
        } else {
            return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
        }

        return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/refuser-echange/{id}/{idp}", name="ally_refuse_exchange", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function allyRefuseExchangeAction(Exchange $id, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly() && $user->getGrade()->getPlacement() == 1) {
            $em->remove($id);
            $em->flush();
        } else {
            return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
        }

        return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/ajouter-des-membres/{idp}", name="ally_page_add", requirements={"idp"="\d+"})
     */
    public function addPageAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $maxMembers = count($user->getAlly()->getUsers()) + count($user->getAlly()->getProposals());
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }

        $form_allyAdd = $this->createForm(AllyAddType::class);
        $form_allyAdd->handleRequest($request);

        if (($form_allyAdd->isSubmitted() && $form_allyAdd->isValid()) && $user->getGrade()->getCanRecruit() == 1) {
            if($maxMembers >= $ally->getMaxMembers()) {
                return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
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
            return $this->redirectToRoute('ally_page_add', ['idp' => $usePlanet->getId()]);
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

        return $this->render('connected/ally/add.html.twig', [
            'usePlanet' => $usePlanet,
            'form_allyAdd' => $form_allyAdd->createView(),
            'usersRecrutable' => $usersRecruitable,
        ]);
    }

    /**
     * @Route("/alliance-level/{idp}", name="ally_level", requirements={"idp"="\d+"})
     */
    public function allylevelAction($idp)
    {
        $user = $this->getUser();
        $ally = $user->getAlly();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($ally->getLevel() == 10) {
            return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
        }
        $array = $ally->getLevelCost();
        if($user->getGrade()->getPlacement() == 1 && $ally->getBitcoin() >= $array[1] && $ally->getBitcoin() >= $array[2]) {
            $ally->setLevel($ally->getLevel() + 1);
            $ally->setMaxMembers($array[0]);
            $ally->setBitcoin($ally->getBitcoin() - $array[1]);
            $ally->setPdg($ally->getPdg() - $array[2]);
            $em->flush();
        } else {
            return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
        }

        return $this->redirectToRoute('ally_page_bank', ['idp' => $usePlanet->getId()]);
    }

    /**
     * @Route("/administration-alliance/{idp}", name="ally_page_admin", requirements={"idp"="\d+"})
     */
    public function adminPageAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $grade = new Grade();
        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
        }

        $form_allyDecon = $this->createForm(AllyDefconType::class,$ally);
        $form_allyDecon->handleRequest($request);

        $form_allyGrade = $this->createForm(AllyGradeType::class,$grade);
        $form_allyGrade->handleRequest($request);

        if (($form_allyDecon->isSubmitted() && $form_allyDecon->isValid())) {
            $em->flush();
        }

        if (($form_allyGrade->isSubmitted() && $form_allyGrade->isValid())) {
            $grade->setAlly($ally);
            $em->persist($grade);
            $ally->addGrade($grade);
            $em->flush();
        }

        return $this->render('connected/ally/admin.html.twig', [
            'usePlanet' => $usePlanet,
            'form_allyGrade' => $form_allyGrade->createView(),
            'form_allyDefcon' => $form_allyDecon->createView()
        ]);
    }

    /**
     * @Route("/ambassade-interne/{idp}", name="ally_page_pacts", requirements={"idp"="\d+"})
     */
    public function pactPageAllyAction(Request $request, $idp)
    {
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        $em = $this->getDoctrine()->getManager();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        if($user->getAlly()) {
            $ally = $user->getAlly();
        } else {
            return $this->redirectToRoute('ally_blank', ['idp' => $usePlanet->getId()]);
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
            $allyPact = $em->getRepository('App:Ally', ['idp' => $usePlanet->getId()])
                ->createQueryBuilder('a')
                ->where('a.sigle = :sigle')
                ->setParameter('sigle', $form_allyPact->get('allyName')->getData())
                ->getQuery()
                ->getOneOrNullResult();

            if((!$allyPact || $user->getAlly()->getAlreadyPact($allyPact->getSigle())) || $allyPact == $ally) {
                return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
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
                /*$salon = new Salon();
                $salon->setName("Guerre : " . $allyPact->getSigle() . " - " . $ally->getSigle());
                $salon->addAlly($allyPact);
                $salon->addAlly($ally);
                $em->persist($salon);*/
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
            return $this->redirectToRoute('ally_page_pacts', ['idp' => $usePlanet->getId()]);
        }

        $allAllys = $em->getRepository('App:Ally')->findAll();

        return $this->render('connected/ally/pact.html.twig', [
            'waitingPna' => $waitingPna,
            'usePlanet' => $usePlanet,
            'waitingAllied' => $waitingAllied,
            'form_allyPact' => $form_allyPact->createView(),
            'allAllys' => $allAllys
        ]);
    }
}