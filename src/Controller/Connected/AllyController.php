<?php

namespace App\Controller\Connected;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use App\Entity\Ally;
use App\Entity\Grade;

/**
 * @Route("/fr")
 * @Security("has_role('ROLE_USER')")
 */
class AllyController extends Controller
{
    /**
     * @Route("/alliance", name="ally")
     * @Route("/alliance/", name="ally_withSlash")
     */
    public function allyAction()
    {
        return $this->render('connected/ally.html.twig');
    }

    /**
     * @Route("/creer-alliance", name="create_ally")
     * @Route("/creer-alliance/", name="create_ally_withSlash")
     */
    public function createAllyAction()
    {
        if($this->getUser()->getAlly()) {
            return $this->render('connected/ally.html.twig');
        }
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $ally = new Ally();
        $grade = new Grade();

        $ally->addUser($user);
        $ally->setTaxe(2);
        $ally->setName('Les baggareurs');
        $ally->setBitcoin(200);
        $ally->setImageName('defaut.jpg');
        $em->persist($ally);

        $grade->setAlly($ally);
        $grade->setName("King");
        $grade->setUser($user);
        $grade->setPlacement(1);
        $grade->setCanRecruit(true);
        $grade->setCanKick(true);
        $grade->setCanWar(true);
        $grade->setCanPeace(true);
        $em->persist($grade);

        $ally->addGrade($grade);
        $user->setAlly($ally);
        $user->setGrade($grade);
        $em->persist($user);
        $em->persist($ally);
        $em->flush();

        return $this->render('connected/ally.html.twig');
    }

    /**
     * @Route("/supprimer-alliance", name="remove_ally")
     * @Route("/supprimer-alliance/", name="remove_ally_withSlash")
     */
    public function removeAllyAction()
    {
        return $this->render('connected/ally.html.twig');
    }
}