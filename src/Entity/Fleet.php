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
     * @ORM\Column(name="name",type="string", length=20)
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
     * @ORM\Column(name="flightType",type="string", nullable=true)
     */
    protected $flightType = null;

    /**
     * @ORM\Column(name="recycleAt",type="datetime", nullable=true)
     */
    protected $recycleAt;

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
     * @ORM\Column(name="cargoI",type="bigint", nullable=true)
     */
    protected $cargoI = 0;

    /**
     * @ORM\Column(name="cargoV",type="bigint", nullable=true)
     */
    protected $cargoV = 0;

    /**
     * @ORM\Column(name="cargoX",type="bigint", nullable=true)
     */
    protected $cargoX = 0;

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
     * @ORM\Column(name="hunterHeavy",type="bigint", nullable=true)
     */
    protected $hunterHeavy = 0;

    /**
     * @ORM\Column(name="corvet",type="bigint", nullable=true)
     */
    protected $corvet = 0;

    /**
     * @ORM\Column(name="corvetLaser",type="bigint", nullable=true)
     */
    protected $corvetLaser = 0;

    /**
     * @ORM\Column(name="fregate",type="bigint", nullable=true)
     */
    protected $fregate = 0;

    /**
     * @ORM\Column(name="fregatePlasma",type="bigint", nullable=true)
     */
    protected $fregatePlasma = 0;

    /**
     * @ORM\Column(name="croiser",type="bigint", nullable=true)
     */
    protected $croiser = 0;

    /**
     * @ORM\Column(name="ironClad",type="bigint", nullable=true)
     */
    protected $ironClad = 0;

    /**
     * @ORM\Column(name="destroyer",type="bigint", nullable=true)
     */
    protected $destroyer = 0;

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
     * @return string
     */
    public function getFleetTags(): string
    {
        if($this->getUser()->getAlly()) {
            $return = "<span class='text-orange'>[" . $this->getUser()->getAlly()->getSigle() . "]" . " " . $this->getUser()->getAlly()->getName() . "</span> - " . $this->getUser()->getUserName() . " -> " . $this->getName();
        } else {
            $return = $this->getUser()->getUserName();
        }
        return $return;
    }

    /**
     * @return string
     */
    public function getShipsReport($malus): string
    {
        $ships = '';
        if($this->getHunter()) {
            $ships = "Chasseurs : " . $this->getHunter() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getHunter() - ($this->getHunter() / $malus)) . "</span></span><br>";
        }
        if($this->getHunterHeavy()) {
            $ships = $ships . "Chasseurs lourds : " . $this->getHunterHeavy() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getHunterHeavy() - ($this->getHunterHeavy() / $malus)) . "</span></span><br>";
        }
        if($this->getCorvet()) {
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getCorvet() - ($this->getCorvet() / $malus)) . "</span></span><br>";
        }
        if($this->getCorvetLaser()) {
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getCorvetLaser() - ($this->getCorvetLaser() / $malus)) . "</span></span><br>";
        }
        if($this->getFregate()) {
            $ships = $ships . "Frégates : " . $this->getFregate() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getFregate() - ($this->getFregate() / $malus)) . "</span></span><br>";
        }
        if($this->getFregatePlasma()) {
            $ships = $ships . "Frégates a plasma : " . $this->getFregatePlasma() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getFregatePlasma() - ($this->getFregatePlasma() / $malus)) . "</span></span><br>";
        }
        if($this->getCroiser()) {
            $ships = $ships . "Croiseurs : " . $this->getCroiser() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getCroiser() - ($this->getCroiser() / $malus)) . "</span></span><br>";
        }
        if($this->getIronClad()) {
            $ships = $ships . "Cuirassés : " . $this->getIronClad() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getIronClad() - ($this->getIronClad() / $malus)) . "</span></span><br>";
        }
        if($this->getDestroyer()) {
            $ships = $ships . "Destroyers : " . $this->getDestroyer() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($this->getDestroyer() - ($this->getDestroyer() / $malus)) . "</span></span><br>";
        }

        return $ships;
    }

    /**
     * @return string
     */
    public function getShipsReportNoLost(): string
    {
        $ships = '';
        if($this->getHunter()) {
            $ships = "Chasseurs : " . $this->getHunter() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getHunterHeavy()) {
            $ships = $ships . "Chasseurs lourds : " . $this->getHunterHeavy() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCorvet()) {
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCorvetLaser()) {
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getFregate()) {
            $ships = $ships . "Frégates : " . $this->getFregate() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getFregatePlasma()) {
            $ships = $ships . "Frégates a plasma : " . $this->getFregatePlasma() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCroiser()) {
            $ships = $ships . "Croiseurs : " . $this->getCroiser() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getIronClad()) {
            $ships = $ships . "Cuirassés : " . $this->getIronClad() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getDestroyer()) {
            $ships = $ships . "Destroyers : " . $this->getDestroyer() . " <span class='float-right'>Perte : Aucune</span><br>";
        }

        return $ships;
    }

    /**
     * @return string
     */
    public function getShipsLoseReport(): string
    {
        $ships = '';
        if($this->getHunter()) {
            $ships = "Chasseurs : " . $this->getHunter() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getHunterHeavy()) {
            $ships = $ships . "Chasseurs lourds : " . $this->getHunterHeavy() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCorvet()) {
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCorvetLaser()) {
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getFregate()) {
            $ships = $ships . "Frégates : " . $this->getFregate() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getFregatePlasma()) {
            $ships = $ships . "Frégates a plasma : " . $this->getFregatePlasma() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCroiser()) {
            $ships = $ships . "Croiseurs : " . $this->getCroiser() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getIronClad()) {
            $ships = $ships . "Cuirassés : " . $this->getIronClad() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getDestroyer()) {
            $ships = $ships . "Destroyers : " . $this->getDestroyer() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }

        return $ships;
    }

    /**
     * @return mixed
     */
    public function getAllianceUser()
    {
        if($this->getUser()->getAlly()) {
            $uAlly = $this->getUser()->getAlly()->getUsers();
            $uFleet = $this->getPlanet()->getUser();
            foreach ($uAlly as $user) {
                if ($uFleet == $user) {
                    return 'hello';
                }
            }
        } elseif ($this->getUser() == $this->getPlanet()->getUser()) {
            return 'hello';
        }
        return null;
    }

    /**
     * @return int
     */
    public function getCargoFull(): int
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
     * @return int
     */
    public function getCargoPlace(): int
    {
        $barge = $this->getBarge() * 2000;
        $recycleur = $this->getRecycleur() * 10000;
        $cargoI = $this->getCargoI() * 25000;
        $cargoV = $this->getCargoV() * 75000;
        $cargoX = $this->getCargoX() * 150000;

        $nbr = $barge + $recycleur + $cargoI + $cargoV + $cargoX;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getArmor(): int
    {
        $hunter = $this->getHunter() * 10;
        $hunterHeavy = $this->getHunterHeavy() * 20;
        $corvet = $this->getCorvet() * 38;
        $corvetLaser = $this->getCorvetLaser() * 53;
        $fregate = $this->getFregate() * 120;
        $fregatePlasma = $this->getFregatePlasma() * 200;
        $croiser = $this->getCroiser() * 500;
        $ironClad = $this->getIronClad() * 1100;
        $destroyer = $this->getDestroyer() * 200;

        $nbr = $fregate + $hunter + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getPlasma(): int
    {
        $fregatePlasma = $this->getFregatePlasma() * 150;
        $croiser = $this->getCroiser() * 100;
        $ironClad = $this->getIronClad() * 200;
        $destroyer = $this->getDestroyer() * 40;

        $nbr = $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getMissile(): int
    {
        $hunter = $this->getHunter() * 9;
        $hunterHeavy = $this->getHunterHeavy() * 10;
        $corvet = $this->getCorvet() * 25;
        $corvetLaser = $this->getCorvetLaser() * 50;
        $fregate = $this->getFregate() * 50;
        $fregatePlasma = $this->getFregatePlasma() * 118;
        $croiser = $this->getCroiser() * 350;
        $ironClad = $this->getIronClad() * 1000;
        $destroyer = $this->getDestroyer() * 3300;

        $nbr = $fregate + $hunter + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getShield(): int
    {
        $corvet = $this->getCorvet() * 2;
        $corvetLaser = $this->getCorvetLaser() * 5;
        $fregate = $this->getFregate() * 20;
        $fregatePlasma = $this->getFregatePlasma() * 80;
        $croiser = $this->getCroiser() * 120;
        $ironClad = $this->getIronClad() * 100;
        $destroyer = $this->getDestroyer() * 2000;

        $nbr = $fregate + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getLaser(): int
    {
        $corvetLaser = $this->getCorvetLaser() * 40;
        $fregate = $this->getFregate() * 34;
        $fregatePlasma = $this->getFregatePlasma() * 10;
        $croiser = $this->getCroiser() * 50;
        $ironClad = $this->getIronClad() * 200;
        $destroyer = $this->getDestroyer() * 40;

        $nbr = $fregate + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getNbrShips(): int
    {
        $sonde = $this->getSonde();
        $colonizer = $this->getColonizer();
        $recycleur = $this->getRecycleur();
        $cargoI = $this->getCargoI();
        $cargoV = $this->getCargoV();
        $cargoX = $this->getCargoX();
        $barge = $this->getBarge();
        $hunter = $this->getHunter();
        $hunterHeavy = $this->getHunterHeavy();
        $corvet = $this->getCorvet();
        $corvetLaser = $this->getCorvetLaser();
        $fregate = $this->getFregate();
        $fregatePlasma = $this->getFregatePlasma();
        $croiser = $this->getCroiser();
        $ironClad = $this->getIronClad();
        $destroyer = $this->getDestroyer();

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde + $cargoI + $cargoV + $cargoX + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getNbrSignatures(): int
    {
        $sonde = $this->getSonde();
        $colonizer = $this->getColonizer() * 200;
        $recycleur = $this->getRecycleur() * 80;
        $cargoI = $this->getCargoI() * 50;
        $cargoV = $this->getCargoV() * 120;
        $cargoX = $this->getCargoX() * 250;
        $barge = $this->getBarge() * 50;
        $hunter = $this->getHunter() * 5;
        $hunterHeavy = $this->getHunterHeavy() * 8;
        $corvet = $this->getCorvet() * 25;
        $corvetLaser = $this->getCorvetLaser() * 40;
        $fregate = $this->getFregate() * 60;
        $fregatePlasma = $this->getFregatePlasma() * 150;
        $croiser = $this->getCroiser() * 300;
        $ironClad = $this->getIronClad() * 700;
        $destroyer = $this->getDestroyer() * 1500;

        $nbr = $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde + $cargoI + $cargoV + $cargoX + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return float
     */
    public function getSpeed(): float
    {
        if($this->getBarge()) {
            return 3;
        }
        if($this->getColonizer()) {
            return 2;
        }
        if($this->getRecycleur() || $this->getIronClad()) {
            return 1.5;
        }
        if($this->getDestroyer()) {
            return 1.4;
        }
        if($this->getCroiser()) {
            return 1.3;
        }
        if($this->getFregatePlasma()) {
            return 1.2;
        }
        if($this->getFregate()) {
            return 1.1;
        }
        if($this->getCorvetLaser()) {
            return 1;
        }
        if($this->getCorvet()) {
            return 0.9;
        }
        if($this->getHunter() || $this->getHunterHeavy()) {
            return 0.8;
        }
        if($this->getCargoX()) {
            return 0.7;
        }
        if($this->getCargoV()) {
            return 0.6;
        }
        if($this->getCargoI()) {
            return 0.5;
        }
        if($this->getSonde()) {
            return 0.01;
        }

        return 1;
    }

    /**
     * @param $percent
     */
    public function setFleetWinRatio($percent): void
    {
        if($percent != 0) {
            if ($this->getHunter()) {
                $new = ($this->getHunter() / $percent);
                $this->setHunter(round($new));
            }
            if ($this->getHunterHeavy()) {
                $new = ($this->getHunterHeavy() / $percent);
                $this->setHunterHeavy(round($new));
            }
            if ($this->getFregate()) {
                $new = ($this->getFregate() / $percent);
                $this->setFregate(round($new));
            }
            if ($this->getCorvet()) {
                $new = ($this->getCorvet() / $percent);
                $this->setCorvet(round($new));
            }
            if ($this->getCorvetLaser()) {
                $new = ($this->getCorvetLaser() / $percent);
                $this->setCorvetLaser(round($new));
            }
            if ($this->getFregatePlasma()) {
                $new = ($this->getFregatePlasma() / $percent);
                $this->setFregatePlasma(round($new));
            }
            if ($this->getCroiser()) {
                $new = ($this->getCroiser() / $percent);
                $this->setCroiser(round($new));
            }
            if ($this->getDestroyer()) {
                $new = ($this->getDestroyer() / $percent);
                $this->setDestroyer(round($new));
            }
            if ($this->getIronClad()) {
                $new = ($this->getIronClad() / $percent);
                $this->setIronClad(round($new));
            }
        }
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
     * @return bool
     */
    public function getAttack(): bool
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
    public function getRecycleAt()
    {
        return $this->recycleAt;
    }

    /**
     * @param mixed $recycleAt
     */
    public function setRecycleAt($recycleAt): void
    {
        $this->recycleAt = $recycleAt;
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

    /**
     * @return mixed
     */
    public function getCargoI()
    {
        return $this->cargoI;
    }

    /**
     * @param mixed $cargoI
     */
    public function setCargoI($cargoI): void
    {
        $this->cargoI = $cargoI;
    }

    /**
     * @return mixed
     */
    public function getCargoV()
    {
        return $this->cargoV;
    }

    /**
     * @param mixed $cargoV
     */
    public function setCargoV($cargoV): void
    {
        $this->cargoV = $cargoV;
    }

    /**
     * @return mixed
     */
    public function getCargoX()
    {
        return $this->cargoX;
    }

    /**
     * @param mixed $cargoX
     */
    public function setCargoX($cargoX): void
    {
        $this->cargoX = $cargoX;
    }

    /**
     * @return mixed
     */
    public function getHunterHeavy()
    {
        return $this->hunterHeavy;
    }

    /**
     * @param mixed $hunterHeavy
     */
    public function setHunterHeavy($hunterHeavy): void
    {
        $this->hunterHeavy = $hunterHeavy;
    }

    /**
     * @return mixed
     */
    public function getCorvet()
    {
        return $this->corvet;
    }

    /**
     * @param mixed $corvet
     */
    public function setCorvet($corvet): void
    {
        $this->corvet = $corvet;
    }

    /**
     * @return mixed
     */
    public function getCorvetLaser()
    {
        return $this->corvetLaser;
    }

    /**
     * @param mixed $corvetLaser
     */
    public function setCorvetLaser($corvetLaser): void
    {
        $this->corvetLaser = $corvetLaser;
    }

    /**
     * @return mixed
     */
    public function getFregatePlasma()
    {
        return $this->fregatePlasma;
    }

    /**
     * @param mixed $fregatePlasma
     */
    public function setFregatePlasma($fregatePlasma): void
    {
        $this->fregatePlasma = $fregatePlasma;
    }

    /**
     * @return mixed
     */
    public function getCroiser()
    {
        return $this->croiser;
    }

    /**
     * @param mixed $croiser
     */
    public function setCroiser($croiser): void
    {
        $this->croiser = $croiser;
    }

    /**
     * @return mixed
     */
    public function getIronClad()
    {
        return $this->ironClad;
    }

    /**
     * @param mixed $ironClad
     */
    public function setIronClad($ironClad): void
    {
        $this->ironClad = $ironClad;
    }

    /**
     * @return mixed
     */
    public function getFlightType()
    {
        return $this->flightType;
    }

    /**
     * @param mixed $flightType
     */
    public function setFlightType($flightType): void
    {
        $this->flightType = $flightType;
    }

    /**
     * @return mixed
     */
    public function getDestroyer()
    {
        return $this->destroyer;
    }

    /**
     * @param mixed $destroyer
     */
    public function setDestroyer($destroyer): void
    {
        $this->destroyer = $destroyer;
    }
}
