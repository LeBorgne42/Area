<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fleet")
 * @ORM\Entity
 */
class Fleet
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name",type="string", length=15)
     * @Assert\NotBlank(message = "required")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fleets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Planet", inversedBy="fleets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    protected $planet;

    /**
     * @ORM\Column(name="oldPlanet",type="integer", nullable=true)
     */
    protected $oldPlanet = null;

    /**
     * @ORM\Column(name="flightTime",type="datetime", nullable=true)
     */
    protected $flightTime = null;

    /**
     * @ORM\OneToOne(targetEntity="Ship", mappedBy="fleet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ship_id", referencedColumnName="id")
     */
    protected $ship;

    /**
     * @ORM\Column(name="soldier",type="integer", nullable=true)
     */
    protected $soldier = null;

    /**
     * @ORM\Column(name="worker",type="integer", nullable=true)
     */
    protected $worker = null;

    /**
     * @ORM\Column(name="scientist",type="integer", nullable=true)
     */
    protected $scientist = null;

    /**
     * @param mixed $soldier
     */
    public function setSoldier($soldier): void
    {
        $this->soldier = $soldier;
    }

    /**
     * @return mixed
     */
    public function getSoldier()
    {
        return $this->soldier;
    }

    /**
     * @param mixed $scientist
     */
    public function setScientist($scientist): void
    {
        $this->scientist = $scientist;
    }

    /**
     * @return mixed
     */
    public function getScientist()
    {
        return $this->scientist;
    }

    /**
     * @return mixed
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param mixed $worker
     */
    public function setWorker($worker): void
    {
        $this->worker = $worker;
    }

    /**
     * @return mixed
     */
    public function getShip()
    {
        return $this->ship;
    }

    /**
     * @param mixed $ship
     */
    public function setShip($ship): void
    {
        $this->ship = $ship;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @param mixed $planet
     */
    public function setPlanet($planet): void
    {
        $this->planet = $planet;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNbrShips()
    {
        $fregate = $this->getShip()->getFregate()->getAmount();
        $colonizer = $this->getShip()->getColonizer()->getAmount();
        $barge = $this->getShip()->getBarge()->getAmount();
        $hunter = $this->getShip()->getHunter()->getAmount();
        $recycleur = $this->getShip()->getRecycleur()->getAmount();
        $sonde = $this->getShip()->getSonde()->getAmount();

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getNbrSignatures()
    {
        $fregate = $this->getShip()->getFregate()->getAmount() * $this->getShip()->getFregate()->getSignature();
        $colonizer = $this->getShip()->getColonizer()->getAmount() * $this->getShip()->getColonizer()->getSignature();
        $barge = $this->getShip()->getBarge()->getAmount() * $this->getShip()->getBarge()->getSignature();
        $hunter = $this->getShip()->getHunter()->getAmount() * $this->getShip()->getHunter()->getSignature();
        $recycleur = $this->getShip()->getRecycleur()->getAmount() * $this->getShip()->getRecycleur()->getSignature();
        $sonde = $this->getShip()->getSonde()->getAmount() * $this->getShip()->getSonde()->getSignature();

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getOldPlanet()
    {
        return $this->oldPlanet;
    }

    /**
     * @param mixed $oldPlanet
     */
    public function setOldPlanet($oldPlanet): void
    {
        $this->oldPlanet = $oldPlanet;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrigin()
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->gt('planet', $this->planet->getOldPlanet()));

        return $this->fleet->matching($criteria);
    }

    /**
     * @return mixed
     */
    public function getFlightTime()
    {
        return $this->flightTime;
    }

    /**
     * @param mixed $flightTime
     */
    public function setFlightTime($flightTime): void
    {
        $this->flightTime = $flightTime;
    }

    public function getId()
    {
        return $this->id;
    }
}
