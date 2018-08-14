<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\UserImageType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use DateTime;
use DateTimeZone;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class OverviewController extends Controller
{
    /**
     * @Route("/empire/{idp}", name="overview", requirements={"idp"="\d+"})
     */
    public function overviewAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $now = new DateTime();
        $now->setTimezone(new DateTimeZone('Europe/Paris'));
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $this->getUser()))
            ->getQuery()
            ->getOneOrNullResult();

        $allPlanets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameters(array('user' => $this->getUser()))
            ->orderBy('p.position')
            ->orderBy('p.sector')
            ->getQuery()
            ->getResult();

        $attackFleets = new \ArrayObject();
        foreach ($allPlanets as $planet) {
            $allFleets = $em->getRepository('App:Fleet')
                ->createQueryBuilder('f')
                ->join('f.sector', 's')
                ->join('s.galaxy', 'g')
                ->where('f.user != :user')
                ->andWhere('f.planete = :planete')
                ->andWhere('s.position = :sector')
                ->andWhere('g.position = :galaxy')
                ->setParameters(array('user' => $user, 'planete' => $planet->getPosition(), 'sector' => $planet->getSector()->getPosition(), 'galaxy' =>$planet->getSector()->getGalaxy()->getPosition()))
                ->orderBy('f.flightTime')
                ->getQuery()
                ->getResult();

            if($allFleets) {
                $attackFleets = $allFleets;
            }
        }
        if (count($attackFleets) == 0) {
            $attackFleets = null;
        }

        $user = $this->getUser();
        $form_image = $this->createForm(UserImageType::class,$user);
        $form_image->handleRequest($request);

        if ($form_image->isSubmitted() && $form_image->isValid()) {
            $em->persist($user);
            $em->flush();
        }

        return $this->render('connected/overview.html.twig', [
            'form_image' => $form_image->createView(),
            'usePlanet' => $usePlanet,
            'date' => $now,
            'fleetMove' => $attackFleets,
        ]);
    }

    /**
     * @Route("/dans-le-cul-lulu/", name="game_over")
     */
    public function gameOverAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        if($user->getGameOver() || $user->getAllPlanets() == 0) {
            if($user->getColPlanets() == 0 && $user->getGameOver() == null) {
                $user->setGameOver($user->getUserName());
                $em->persist($user);
                $em->flush();
            }
            if($user->getRank()) {
                $user->setBitcoin(25000);
                $user->setAlly(null);
                $user->setSearch(null);
                $user->setRank(null);
                $user->setGrade(null);
                $user->setJoinAllyAt(null);
                $user->setAllyBan(null);
                $user->setScientistProduction(1);
                $user->setSearchAt(null);
                /*$user->setPlasma(0);
                $user->setLaser(0);
                $user->setMissile(0);
                $user->setArmement(0);
                $user->setRecycleur(0);
                $user->setCargo(0);
                $user->setTerraformation(0);
                $user->setDemography(0);
                $user->setUtility(0);
                $user->setBarge(0);
                $user->setHyperespace(0);
                $user->setDiscipline(0);
                $user->setHeavyShip(0);
                $user->setLightShip(0);
                $user->setIndustry(0);
                $user->setOnde(0);
                $user->setHyperespace(0);
                $user->setDiscipline(0);*/
                foreach ($user->getSalons() as $salon) {
                    $salon->removeUser($user);
                }

                $salon = $em->getRepository('App:Salon')
                    ->createQueryBuilder('s')
                    ->where('s.name = :name')
                    ->setParameters(array('name' => 'Public'))
                    ->getQuery()
                    ->getOneOrNullResult();

                $salon->removeUser($user);
                $em->persist($salon);
                $user->setSalons(null);
                $em->persist($user);
                $em->flush();
            }
            return $this->render('connected/game_over.html.twig', [
            ]);
        } else {
            return $this->redirectToRoute('home');
        }
    }
}