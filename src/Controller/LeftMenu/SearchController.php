<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class SearchController extends Controller
{
    /**
     * @Route("/recherche", name="search")
     * @Route("/recherche/", name="search_withSlash")
     */
    public function searchAction()
    {
        return $this->render('left_menu/search.html.twig');
    }
}