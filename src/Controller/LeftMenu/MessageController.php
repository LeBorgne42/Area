<?php

namespace App\Controller\LeftMenu;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/a")
 * @Security("has_role('ROLE_USER')")
 */
class MessageController extends Controller
{
    /**
     * @Route("/message", name="message")
     * @Route("/message/", name="message_withSlash")
     */
    public function messageAction()
    {
        return $this->render('left_menu/message.html.twig');
    }
}