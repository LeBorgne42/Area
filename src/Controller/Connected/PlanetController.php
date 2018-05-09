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

        $usePlanet = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.id = :id')
            ->andWhere('p.user = :user')
            ->setParameters(array('id' => $idp, 'user' => $user))
            ->getQuery()
            ->getOneOrNullResult();

        $allPlanets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->where('p.user = :user')
            ->setParameters(array('user' => $user))
            ->orderBy('p.position')
            ->orderBy('p.sector')
            ->getQuery()
            ->getResult();

        $form_manageRenamePlanet = $this->createForm(PlanetRenameType::class);
        $form_manageRenamePlanet->handleRequest($request);

        if ($form_manageRenamePlanet->isSubmitted() && $form_manageRenamePlanet->isValid()) {
            $renamePlanet = $em->getRepository('App:Planet')
                ->createQueryBuilder('p')
                ->where('p.id = :id')
                ->andWhere('p.user = :user')
                ->setParameters(array('id' => $form_manageRenamePlanet->get('id')->getData(), 'user' => $user))
                ->getQuery()
                ->getOneOrNullResult();

            $renamePlanet->setName($form_manageRenamePlanet->get('name')->getData());
            $em->persist($renamePlanet);
            $em->flush();
            return $this->redirectToRoute('planet', array('idp' => $usePlanet->getId()));
        }

        return $this->render('connected/planet.html.twig', [
            'usePlanet' => $usePlanet,
            'allPlanets' => $allPlanets,
            'formObject' => $form_manageRenamePlanet,
        ]);
    }
}