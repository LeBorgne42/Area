<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use DateTime;
use DateTimeZone;
use Dateinterval;

/**
 * Class KernelControllerListener
 *
 *      app.listener.kernel_controller:
 *          class: AppBundle\EventListener\KernelControllerListener
 *          arguments: ['@security.token_storage', '@doctrine.orm.entity_manager']
 *          tags:
 *              - { name: kernel.event_subscriber }
 *
 *
 * @package AppBundle\EventListener
 */
class UserEvent implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $token;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * KernelControllerListener constructor.
     * @param TokenStorageInterface $token
     * @param EntityManagerInterface $em
     */
    public function __construct(TokenStorageInterface $token, EntityManagerInterface $em)
    {
        $this->token = $token;
        $this->em = $em;
    }
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(KernelEvents::CONTROLLER => "onCoreController");
    }
    /**
     * @param FilterControllerEvent $event
     */
    public function onCoreController(FilterControllerEvent $event)
    {
        if($event->getRequestType() == HttpKernel::MASTER_REQUEST)
        {
            if($this->token->getToken()) {
                $user = $this->token->getToken()->getUser();
                $now = new DateTime();
                $now->setTimezone(new DateTimeZone('Europe/Paris'));

                if ($user instanceof User) {
                    if($user->getSearchAt()) {
                        if ($user->getSearchAt()->format('U') < $now->format('U')) {
                            $research = $user->getSearch();
                            if ($research == 'onde') {
                                $user->setOnde($user->getOnde() + 1);
                            } elseif ($research == 'industry') {
                                $user->setIndustry($user->getIndustry() + 1);
                            } elseif ($research == 'discipline') {
                                $user->setDiscipline($user->getDiscipline() + 1);
                            } elseif ($research == 'hyperespace') {
                                $user->setHyperespace(1);
                            } elseif ($research == 'barge') {
                                $user->setBarge(1);
                            } elseif ($research == 'utility') {
                                $user->setUtility($user->getUtility() + 1);
                            } elseif ($research == 'demography') {
                                $user->setDemography($user->getDemography() + 1);
                            } elseif ($research == 'terraformation') {
                                $user->setTerraformation($user->getTerraformation() + 1);
                            } elseif ($research == 'cargo') {
                                $user->setCargo($user->getCargo() + 1);
                            } elseif ($research == 'recycleur') {
                                $user->setRecycleur(1);
                            } elseif ($research == 'armement') {
                                $user->setArmement($user->getArmement() + 1);
                            } elseif ($research == 'missile') {
                                $user->setMissile($user->getMissile() + 1);
                            } elseif ($research == 'laser') {
                                $user->setLaser($user->getLaser() + 1);
                            } elseif ($research == 'plasma') {
                                $user->setPlasma($user->getPlasma() + 1);
                            } elseif ($research == 'lightShip') {
                                $user->setLightShip($user->getLightShip() + 1);
                            } elseif ($research == 'heavyShip') {
                                $user->setHeavyShip($user->getHeavyShip() + 1);
                            }
                            $user->setSearch(null);
                            $user->setSearchAt(null);
                            $server = $this->em->getRepository('App:Server')->find(['id' => 1]);
                            $server->setNbrResearch($server->getNbrResearch() + 1);
                            $quest = $user->checkQuests('research');
                            if($quest) {
                                $user->removeQuest($quest);
                                $user->getRank()->setWarPoint($user->getRank()->getWarPoint() + 1000);
                            }
                            $this->em->flush($server);
                            $this->em->flush($user);
                        }
                    }
                    if ($user->getLastActivity()) {
                        $seconds = ($now->format('U') - ($user->getLastActivity()->format('U')));
                    } else {
                        $user->setLastActivity($now);
                        $this->em->flush($user);
                        $seconds = ($now->format('U') - ($user->getLastActivity()->format('U')));
                    }
                    if ($seconds >= 60) {
                        if (!$user->getSpecUsername()) {
                            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                                $userIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
                            } else {
                                $userIp = $_SERVER['REMOTE_ADDR'];
                            }
                            $userSameIp = $this->em->getRepository('App:User')
                                ->createQueryBuilder('u')
                                ->where('u.ipAddress = :ip')
                                ->andWhere('u.username != :user')
                                ->setParameters(['user' => $user->getUsername(), 'ip' => $userIp])
                                ->getQuery()
                                ->getOneOrNullResult();

                            if ($userSameIp) {
                                $user->setIpAddress($userSameIp->getUsername() . '-' . rand(1, 99));
                                $user->setCheat($user->getCheat() + 1);
                                $userSameIp->setCheat($user->getCheat() + 1);
                                $this->em->flush($userSameIp);
                            } else {
                                $user->setIpAddress($userIp);
                            }
                        }
                        foreach ($user->getPlanets() as $planet) {
                            $nbProd = ($planet->getNbProduction() * $seconds) / 60;
                            $wtProd = ($planet->getWtProduction() * $seconds) / 60;
                            if ($planet->getUser()->getAlly()) {
                                $niobium = $planet->getNiobium() + ($nbProd * 1.2);
                                $water = $planet->getWater() + ($wtProd * 1.2);
                            } else {
                                $niobium = $planet->getNiobium() + $nbProd;
                                $water = $planet->getWater() + $wtProd;
                            }
                            if ($planet->getNiobiumMax() > ($planet->getNiobium() + $nbProd)) {
                                $planet->setNiobium($niobium);
                            } else {
                                $planet->setNiobium($planet->getNiobiumMax());
                            }
                            if ($planet->getWaterMax() > ($planet->getWater() + $wtProd)) {
                                $planet->setWater($water);
                            } else {
                                $planet->setWater($planet->getWaterMax());
                            }
                            $this->em->flush($planet);
                        }
                        $user->setLastActivity($now);
                        $this->em->flush($user);
                    }
                }
            }
        }
    }
}