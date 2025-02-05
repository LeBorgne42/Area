<?php

namespace App\Controller\Track;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Track;

/**
 * Class TrackController
 * @package App\Controller\Track
 */
class TrackController extends AbstractController
{
    /**
     * @param ManagerRegistry $doctrine
     * @param $currentPage
     * @return Response
     */
    public function trackAction(ManagerRegistry $doctrine, $currentPage): Response
    {
        $user = $this->getUser();

        $u_agent = substr($_SERVER['HTTP_USER_AGENT'], 0, 35);
        if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip  = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        if ($user && $user->getUsername() == 'Dev' || $user && $user->getUsername() == 'Admin'
            || stripos(strtoupper($u_agent), 'BOT') !== FALSE) {
            return new Response ("");
        }
        $em = $doctrine->getManager();
        $track = new Track();

        if ($user) {
            $track->setUsername($this->getUser()->getUsername());
        }
        $encryptedIp = openssl_encrypt($ip, "AES-256-CBC", "my personal ip", 0, hex2bin('34857d973953e44afb49ea9d61104d8c'));
        $track->setIp($encryptedIp);

        if (stripos(strtoupper($u_agent), 'ANDROID') !== FALSE) {
            $track->setComputer('Android');
        } elseif (stripos(strtoupper($u_agent), 'WINDOWS') !== FALSE) {
            $track->setComputer('Windows');
        } elseif (stripos(strtoupper($u_agent), 'MACINTOSH') !== FALSE) {
            $track->setComputer('Mac');
        } elseif (stripos(strtoupper($u_agent), 'LINUX') !== FALSE) {
            $track->setComputer('Linux');
        } elseif (stripos(strtoupper($u_agent), 'IPHONE') !== FALSE) {
            $track->setComputer('Iphone');
        } elseif (stripos(strtoupper($u_agent), 'IPAD') !== FALSE) {
            $track->setComputer('Ipad');
        } elseif (stripos(strtoupper($u_agent), 'FACEBOOK') !== FALSE) {
            $track->setComputer('Facebook');
        }
        $track->setBrowser($u_agent);

        if (isset($_SERVER['HTTP_REFERER'])) {
            if (preg_match('/' . $_SERVER['HTTP_HOST'] . '/', $_SERVER['HTTP_REFERER'])) {
                $referer ='';
            }
            else {
                $referer = substr($_SERVER['HTTP_REFERER'], 0, 45);
            }
        }
        else {
            $referer ='';
        }
        if (stripos(strtoupper($referer), 'FACEBOOK') !== FALSE) {
            $track->setPreviousPage('Facebook');
        } elseif (stripos(strtoupper($referer), 'GOOGLE') !== FALSE) {
            $track->setPreviousPage('Google');
        } elseif (stripos(strtoupper($referer), 'JEUXVIDEO') !== FALSE) {
            $track->setPreviousPage('JeuxVideo');
        } elseif (stripos(strtoupper($referer), 'BING') !== FALSE) {
            $track->setPreviousPage('Bing');
        } elseif (stripos(strtoupper($referer), 'MRIVIERE') !== FALSE) {
            $track->setPreviousPage('Mriviere');
        } elseif (stripos(strtoupper($referer), 'AREAUNIVERSE') !== FALSE) {
            $track->setPreviousPage('AreaUniverse');
        } elseif (stripos(strtoupper($referer), 'INSTAGRAM') !== FALSE) {
            $track->setPreviousPage('Instagram');
        } elseif (stripos(strtoupper($referer), '.XYZ') !== FALSE) {
            $track->setPreviousPage('Xyz');
        } elseif (stripos(strtoupper($referer), '82721527') !== FALSE) {
            $track->setPreviousPage('82721527');
        } elseif (stripos(strtoupper($referer), 'ESLGAMING') !== FALSE) {
            $track->setPreviousPage('Esl');
        } elseif (stripos(strtoupper($referer), 'LINKEDIN') !== FALSE) {
            $track->setPreviousPage('Linkedin');
        } elseif (stripos(strtoupper($referer), 'LNKD') !== FALSE) {
            $track->setPreviousPage('Linkedin');
        } elseif (stripos(strtoupper($referer), 'TELEGRAM') !== FALSE) {
            $track->setPreviousPage('Telegram');
        } elseif (stripos(strtoupper($referer), 'AVENOEL') !== FALSE) {
            $track->setPreviousPage('Avenoel');
        } else {
            $track->setPreviousPage($referer);
        }

        $track->setPage($currentPage);

        $em->persist($track);
        $em->flush($track);

        return new Response ("");
    }
}