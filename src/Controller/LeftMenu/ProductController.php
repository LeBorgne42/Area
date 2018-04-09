<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class ProductController extends Controller
{
    /**
     * @Route("/production", name="product")
     * @Route("/production/", name="product_withSlash")
     */
    public function productAction()
    {
        return $this->render('left_menu/product.html.twig');
    }
}