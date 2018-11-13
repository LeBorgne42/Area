<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\PlanetRenameType;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class PlanetController extends Controller
{
    /**
     * @Route("/planete/{idp}", name="planet", requirements={"idp"="\d+"})
     */
    public function planetAction(Request $request, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if($user->getGameOver()) {
            return $this->redirectToRoute('game_over');
        }

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_manageRenamePlanet = $this->createForm(PlanetRenameType::class);
        $form_manageRenamePlanet->handleRequest($request);

        if ($form_manageRenamePlanet->isSubmitted() && $form_manageRenamePlanet->isValid()) {
            $renamePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.id = :id')
                ->andWhere('p.user = :user')
                ->setParameters(['id' => $form_manageRenamePlanet->get('id')->getData(), 'user' => $user])
                ->getQuery()
                ->getOneOrNullResult();

            $renamePlanet->setName($form_manageRenamePlanet->get('name')->getData());

            $em->flush();
            return $this->redirectToRoute('planet', ['idp' => $usePlanet->getId()]);
        }

        return $this->render('connected/planet.html.twig', [
            'usePlanet' => $usePlanet,
            'formObject' => $form_manageRenamePlanet,
        ]);
    }

    /**
     * @Route("/planete-abandon/{idp}/{id}", name="planet_abandon", requirements={"idp"="\d+","id"="\d+"})
     */
    public function planetAbandonAction($idp, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $abandonPlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(['id' => $id, 'user' => $user])
            ->getQuery()
            ->getOneOrNullResult();

        $fleetComing = $em->getRepository('App:Fleet')
            ->createQueryBuilder('f')
            ->join('f.sector', 's')
            ->join('s.galaxy', 'g')
            ->where('f.planete = :planete')
            ->andWhere('s.position = :sector')
            ->andWhere('g.position = :galaxy')
            ->andWhere('f.user != :user')
            ->andWhere('f.attack = :true')
            ->setParameters(['planete' => $abandonPlanet->getPosition(), 'true' => 1, 'sector' => $abandonPlanet->getSector()->getPosition(), 'galaxy' => $abandonPlanet->getSector()->getGalaxy()->getPosition(), 'user' => $user])
            ->getQuery()
            ->getResult();

        if($abandonPlanet->getFleetsAbandon($user) == 1 || $fleetComing) {
            return $this->redirectToRoute('planet', ['idp' => $usePlanet->getId()]);
        }

        if($abandonPlanet->getSky() == 10 && $abandonPlanet->getGround() == 60) {
            if($abandonPlanet->getWorker() < 10000) {
                $abandonPlanet->setWorker(10000);
            }
            $abandonPlanet->setUser(null);
            $abandonPlanet->setName('Abandonnée');
        } else {
            $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);
            $hydra = $em->getRepository('App:User')->find(['id' => 1]);

            $abandonPlanet->setUser($hydra);
            $abandonPlanet->setWorker(100000);
            $abandonPlanet->setSoldier(2500);
            $abandonPlanet->setName('Base avancée');
        }

        $em->flush();

        if($user->getColPlanets() == 0) {
            foreach ($user->getFleets() as $fleet) {
                $fleet->setUser($hydra);
                $fleet->setName('Incursion H');
                $fleet->setAttack(true);
            }

            $em->flush();

            return $this->redirectToRoute('game_over');
        }

        return $this->redirectToRoute('planet', ['idp' => $usePlanet->getId()]);
    }
}