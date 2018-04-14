<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/fr")
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
        return $this->render('connected/message.html.twig');
    }
}