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
     * @ORM\Column(name="attack",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $attack = false;

    /**
     * @ORM\Column(name="fightAt",type="datetime", nullable=true)
     */
    protected $fightAt = null;

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
     * @ORM\Column(name="newPlanet",type="integer", nullable=true)
     */
    protected $newPlanet = null;

    /**
     * @ORM\Column(name="flightTime",type="datetime", nullable=true)
     */
    protected $flightTime = null;

    /**
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="fleets", fetch="EXTRA_LAZY")
     */
    protected $sector;

    /**
     * @ORM\Column(name="planete",type="integer", nullable=true)
     */
    protected $planete = null;

    /**
     * @ORM\Column(name="sonde",type="bigint", nullable=true)
     */
    protected $sonde = 0;

    /**
     * @ORM\Column(name="colonizer",type="integer", nullable=true)
     */
    protected $colonizer = 0;

    /**
     * @ORM\Column(name="recycleur",type="integer", nullable=true)
     */
    protected $recycleur = 0;

    /**
     * @ORM\Column(name="barge",type="integer", nullable=true)
     */
    protected $barge = 0;

    /**
     * @ORM\Column(name="hunter",type="bigint", nullable=true)
     */
    protected $hunter = 0;

    /**
     * @ORM\Column(name="fregate",type="bigint", nullable=true)
     */
    protected $fregate = 0;

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
     * @ORM\Column(name="niobium",type="integer", nullable=true)
     */
    protected $niobium = null;

    /**
     * @ORM\Column(name="water",type="integer", nullable=true)
     */
    protected $water = null;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAllianceUser()
    {
        $uAlly = $this->getUser()->getAlly()->getUsers();
        $uFleet = $this->getPlanet()->getUser();
        foreach ($uAlly as $user) {
            if($uFleet == $user) {
                return 'hello';
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getNbrShips()
    {
        $fregate = $this->getFregate();
        $colonizer = $this->getColonizer();
        $barge = $this->getBarge();
        $hunter = $this->getHunter();
        $recycleur = $this->getRecycleur();
        $sonde = $this->getSonde();

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getCargoFull()
    {
        $worker = $this->getWorker();
        $soldier = $this->getSoldier();
        $scientist = $this->getScientist();
        $niobium = $this->getNiobium();
        $water = $this->getWater();

        $nbr = $worker + $soldier + $scientist + $niobium + $water;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getCargoPlace()
    {
        $barge = $this->getBarge() * $this->getBargeCargo();
        $recycleur = $this->getRecycleur() * $this->getRecycleurCargo();

        $nbr = $barge + $recycleur;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getBargeCargo()
    {
        return 2000;
    }

    /**
     * @return mixed
     */
    public function getRecycleurCargo()
    {
        return 10000;
    }

    /**
     * @return mixed
     */
    public function getArmor()
    {
        $fregate = $this->getFregate() * $this->getFregateArmor();
        $hunter = $this->getHunter() * $this->getHunterArmor();
        $nbr = $fregate + $hunter;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getFregateArmor()
    {
        return 120;
    }

    /**
     * @return mixed
     */
    public function getHunterArmor()
    {
        return 10;
    }

    /**
     * @return mixed
     */
    public function getMissile()
    {
        $fregate = $this->getFregate() * $this->getFregateMissile();
        $hunter = $this->getHunter() * $this->getHunterMissile();
        $nbr = $fregate + $hunter;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getFregateMissile(): int
    {
        return 50;
    }

    /**
     * @return mixed
     */
    public function getHunterMissile()
    {
        return 6;
    }

    /**
     * @return mixed
     */
    public function getShield()
    {
        $fregate = $this->getFregate() * $this->getFregateShield();

        $nbr = $fregate;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getFregateShield()
    {
        return 20;
    }

    /**
     * @return mixed
     */
    public function getLaser()
    {
        $fregate = $this->getFregate() * $this->getFregateLaser();

        $nbr = $fregate;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getFregateLaser()
    {
        return 34;
    }

    /**
     * @return mixed
     */
    public function getNbrSignatures()
    {
        $fregate = $this->getFregate() * $this->getFregateSignature();
        $colonizer = $this->getColonizer() * $this->getColonizerSignature();
        $barge = $this->getBarge() * $this->getBargeSignature();
        $hunter = $this->getHunter() * $this->getHunterSignature();
        $recycleur = $this->getRecycleur() * $this->getRecycleurSignature();
        $sonde = $this->getSonde();

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getBargeSignature()
    {
        return 50;
    }

    /**
     * @return mixed
     */
    public function getColonizerSignature()
    {
        return 200;
    }

    /**
     * @return mixed
     */
    public function getFregateSignature()
    {
        return 60;
    }

    /**
     * @return mixed
     */
    public function getHunterSignature()
    {
        return 3;
    }

    /**
     * @return mixed
     */
    public function getRecycleurSignature()
    {
        return 80;
    }

    /**
     * @return mixed
     */
    public function getSpeed()
    {
        if($this->getColonizer()) {
            return 3;
        }
        if($this->getBarge()) {
            return 3;
        }
        if($this->getRecycleur()) {
            return 2;
        }
        if($this->getFregate()) {
            return 1.2;
        }
        if($this->getHunter()) {
            return 0.8;
        }
        if($this->getSonde()) {
            return 0.01;
        }

        return 1;
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
    public function getAttack()
    {
        return $this->attack;
    }

    /**
     * @param mixed $attack
     */
    public function setAttack($attack): void
    {
        $this->attack = $attack;
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

    /**
     * @return mixed
     */
    public function getSonde()
    {
        return $this->sonde;
    }

    /**
     * @param mixed $sonde
     */
    public function setSonde($sonde): void
    {
        $this->sonde = $sonde;
    }

    /**
     * @return mixed
     */
    public function getColonizer()
    {
        return $this->colonizer;
    }

    /**
     * @param mixed $colonizer
     */
    public function setColonizer($colonizer): void
    {
        $this->colonizer = $colonizer;
    }

    /**
     * @return mixed
     */
    public function getRecycleur()
    {
        return $this->recycleur;
    }

    /**
     * @param mixed $recycleur
     */
    public function setRecycleur($recycleur): void
    {
        $this->recycleur = $recycleur;
    }

    /**
     * @return mixed
     */
    public function getBarge()
    {
        return $this->barge;
    }

    /**
     * @param mixed $barge
     */
    public function setBarge($barge): void
    {
        $this->barge = $barge;
    }

    /**
     * @return mixed
     */
    public function getHunter()
    {
        return $this->hunter;
    }

    /**
     * @param mixed $hunter
     */
    public function setHunter($hunter): void
    {
        $this->hunter = $hunter;
    }

    /**
     * @return mixed
     */
    public function getFregate()
    {
        return $this->fregate;
    }

    /**
     * @param mixed $fregate
     */
    public function setFregate($fregate): void
    {
        $this->fregate = $fregate;
    }

    /**
     * @return mixed
     */
    public function getSoldier()
    {
        return $this->soldier;
    }

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
    public function getScientist()
    {
        return $this->scientist;
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
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * @param mixed $sector
     */
    public function setSector($sector): void
    {
        $this->sector = $sector;
    }

    /**
     * @return mixed
     */
    public function getPlanete()
    {
        return $this->planete;
    }

    /**
     * @param mixed $planete
     */
    public function setPlanete($planete): void
    {
        $this->planete = $planete;
    }

    /**
     * @return mixed
     */
    public function getNewPlanet()
    {
        return $this->newPlanet;
    }

    /**
     * @param mixed $newPlanet
     */
    public function setNewPlanet($newPlanet): void
    {
        $this->newPlanet = $newPlanet;
    }

    /**
     * @return mixed
     */
    public function getFightAt()
    {
        return $this->fightAt;
    }

    /**
     * @param mixed $fightAt
     */
    public function setFightAt($fightAt): void
    {
        $this->fightAt = $fightAt;
    }

    /**
     * @return mixed
     */
    public function getNiobium()
    {
        return $this->niobium;
    }

    /**
     * @param mixed $niobium
     */
    public function setNiobium($niobium): void
    {
        $this->niobium = $niobium;
    }

    /**
     * @return mixed
     */
    public function getWater()
    {
        return $this->water;
    }

    /**
     * @param mixed $water
     */
    public function setWater($water): void
    {
        $this->water = $water;
    }

}
