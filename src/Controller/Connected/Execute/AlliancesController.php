<?php

namespace App\Controller\Connected\Execute;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AlliancesController
 * @package App\Controller\Connected\Execute
 */
class AlliancesController extends AbstractController
{
    /**
     * @param $pacts
     * @param $em
     * @return Response
     */
    public function pactsAction($pacts, $em): Response
    {
        foreach ($pacts as $pact) {
            $otherAlly = $doctrine->getRepository(Ally::class)
                ->createQueryBuilder('a')
                ->where('a.sigle = :sigle')
                ->setParameter('sigle', $pact->getAllyTag())
                ->getQuery()
                ->getOneOrNullResult();

            $pact2 = $doctrine->getRepository(Allied::class)
                ->createQueryBuilder('al')
                ->where('al.allyTag = :allytag')
                ->andWhere('al.ally = :ally')
                ->setParameters([
                    'allytag' => $pact->getDismissBy(),
                    'ally' => $otherAlly])
                ->getQuery()
                ->getOneOrNullResult();

            $salons = $doctrine->getRepository(Salon::class)
                ->createQueryBuilder('s')
                ->where('s.name = :sigle1')
                ->orWhere('s.name = :sigle2')
                ->setParameters(['sigle1' => $otherAlly->getSigle() . " - " . $pact->getDismissBy(), 'sigle2' => $pact->getDismissBy() . " - " . $otherAlly->getSigle()])
                ->getQuery()
                ->getResult();

            foreach($salons as $salon) {
                foreach($salon->getContents() as $content) {
                    $em->remove($content);
                }
                foreach ($salon->getViews() as $view) {
                    $em->remove($view);
                }
                $em->remove($salon);
            }

            if($pact2) {
                $em->remove($pact2);
            }
            $em->remove($pact);
        }
        $em->flush();
        echo "Flush -> " . count($pacts) . " ";

        return new Response ("<span style='color:#008000'>OK</span><br/>");
    }

    /**
     * @param $peaces
     * @param $em
     * @return Response
     */
    public function peacesAction($peaces, $em): Response
    {
        foreach ($peaces as $peace) {
            $em->remove($peace);
        }
        echo "Flush -> " . count($peaces) . " ";

        $em->flush();

        return new Response ("<span style='color:#008000'>OK</span><br/>");
    }
}