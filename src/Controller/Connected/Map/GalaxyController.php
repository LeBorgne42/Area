<?php

namespace App\Controller\Connected\Map;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use App\Form\Front\NavigateType;

/**
 * @Route("/connect")
 * @Security("is_granted('ROLE_USER')")
 */
class GalaxyController extends AbstractController
{
    /**
     * @Route("/galaxie/{id}/{idp}", name="galaxy", requirements={"id"="\d+", "idp"="\d+"})
     */
    public function galaxyAction(Request $request, $id, $idp)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $usePlanet = $em->getRepository('App:Planet')->findByCurrentPlanet($idp, $user);

        $form_navigate = $this->createForm(NavigateType::class, null, ["galaxy" => $id, "sector" => 0]);
        $form_navigate->handleRequest($request);

        if ($form_navigate->isSubmitted() && $form_navigate->isValid()) {
            if ($form_navigate->get('sector')->getData() && $form_navigate->get('galaxy')->getData()) {
                return $this->redirectToRoute('map', ['idp' => $usePlanet->getId(), 'id' => $form_navigate->get('sector')->getData(), 'gal' => $form_navigate->get('galaxy')->getData()]);
            }
            return $this->redirectToRoute('galaxy', ['idp' => $usePlanet->getId(), 'id' => $form_navigate->get('galaxy')->getData()]);
        }

  /*      $sectors = $em->getRepository('App:Sector')
            ->createQueryBuilder('s')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->orderBy('s.position', 'ASC')
            ->getQuery()
            ->getResult();*/

        $planets = $em->getRepository('App:Planet')
            ->createQueryBuilder('p')
            ->select('p.merchant, p.cdr, p.empty, s.position as sector, g.position as galaxy, u.username as username, a.sigle as alliance, s.destroy as destroy, u.zombie as zombie')
            ->leftJoin('p.user', 'u')
            ->leftJoin('u.ally', 'a')
            ->join('p.sector', 's')
            ->join('s.galaxy', 'g')
            ->where('g.position = :id')
            ->setParameter('id', $id)
            ->orderBy('p.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('connected/map/galaxy.html.twig', [
            'form_navigate' => $form_navigate->createView(),
//            'sectors' => $sectors,
            'planets' => $planets,
            'usePlanet' => $usePlanet,
        ]);
    }
}