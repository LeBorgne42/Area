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
     * @ORM\OneToOne(targetEntity="Commander", inversedBy="fleet", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="commander_id", referencedColumnName="id")
     */
    protected $commander;

    /**
     * @ORM\Column(name="newPlanet",type="integer", nullable=true)
     */
    protected $newPlanet = null;

    /**
     * @ORM\Column(name="flightTime",type="datetime", nullable=true)
     */
    protected $flightTime = null;

    /**
     * @ORM\Column(name="cancelFlight",type="datetime", nullable=true)
     */
    protected $cancelFlight = null;

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
     * @ORM\Column(name="moonMaker",type="integer", nullable=true)
     */
    protected $moonMaker = 0;

    /**
     * @ORM\Column(name="radarShip",type="integer", nullable=true)
     */
    protected $radarShip = 0;

    /**
     * @ORM\Column(name="brouilleurShip",type="integer", nullable=true)
     */
    protected $brouilleurShip = 0;

    /**
     * @ORM\Column(name="motherShip",type="integer", nullable=true)
     */
    protected $motherShip = 0;

    /**
     * @ORM\Column(name="hunter",type="bigint", nullable=true)
     */
    protected $hunter = 0;

    /**
     * @ORM\Column(name="hunterHeavy",type="bigint", nullable=true)
     */
    protected $hunterHeavy = 0;

    /**
     * @ORM\Column(name="hunterWar",type="bigint", nullable=true)
     */
    protected $hunterWar = 0;

    /**
     * @ORM\Column(name="corvet",type="bigint", nullable=true)
     */
    protected $corvet = 0;

    /**
     * @ORM\Column(name="corvetLaser",type="bigint", nullable=true)
     */
    protected $corvetLaser = 0;

    /**
     * @ORM\Column(name="corvetWar",type="bigint", nullable=true)
     */
    protected $corvetWar = 0;

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
        $attack = '';
        if($this->getAttack() == 1) {
            $attack = "<span class='text-rouge'> [Attaque]</span>";
        }
        if($this->getUser()->getAlly()) {
            $return = "<span class='text-orange'>[" . $this->getUser()->getAlly()->getSigle() . "]" . " " . $this->getUser()->getAlly()->getName() . "</span> - " . $this->getUser()->getUserName() . " -> " . $this->getName() . $attack;
        } else {
            $return = $this->getUser()->getUserName();
        }
        return $return;
    }

    /**
     * @param $armor
     * @return string
     */
    public function getShipsReport($armor): string
    {
        $ships = '';
        if($this->getMotherShip() == 1) {
            $armor = $armor * 0.05;
        }
        if($this->getHunter()) {
            $new = (($this->getHunter() * 15) - $armor) / 15;
            if($new < 0) {
                $armor = $new * -15;
                $new = $this->getHunter();
            } else {
                $armor = 0;
                $new = $this->getHunter() - $new;
            }
            $ships = "Chasseurs : " . $this->getHunter() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getCorvet()) {
            if($armor != 0) {
                $new = (($this->getCorvet() * 74) - $armor) / 74;
                if($new < 0) {
                    $armor = $new * -74;
                    $new = $this->getCorvet();
                } else {
                    $armor = 0;
                    $new = $this->getCorvet() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getHunterHeavy()) {
            if($armor != 0) {
                $new = (($this->getHunterHeavy() * 25) - $armor) / 25;
                if ($new < 0) {
                    $armor = $new * -25;
                    $new = $this->getHunterHeavy();
                } else {
                    $armor = 0;
                    $new = $this->getHunterHeavy() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Chasseurs lourds : " . $this->getHunterHeavy() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getFregate()) {
            if($armor != 0) {
                $new = (($this->getFregate() * 168) - $armor) / 168;
                if ($new < 0) {
                    $armor = $new * -168;
                    $new = $this->getFregate();
                } else {
                    $armor = 0;
                    $new = $this->getFregate() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Frégates : " . $this->getFregate() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getHunterWar()) {
            if($armor != 0) {
                $new = (($this->getHunterWar() * 53) - $armor) / 53;
                if($new < 0) {
                    $armor = $new * -53;
                    $new = $this->getHunterWar();
                } else {
                    $armor = 0;
                    $new = $this->getHunterWar() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Chasseur à plasma : " . $this->getHunterWar() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getCorvetLaser()) {
            if($armor != 0) {
                $new = (($this->getCorvetLaser() * 115) - $armor) / 115;
                if ($new < 0) {
                    $armor = $new * -115;
                    $new = $this->getCorvetLaser();
                } else {
                    $armor = 0;
                    $new = $this->getCorvetLaser() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getCorvetWar()) {
            if($armor != 0) {
                $new = (($this->getCorvetWar() * 135) - $armor) / 135;
                if ($new < 0) {
                    $armor = $new * -135;
                    $new = $this->getCorvetWar();
                } else {
                    $armor = 0;
                    $new = $this->getCorvetWar() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Corvettes Armageddon : " . $this->getCorvetWar() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getFregatePlasma()) {
            if($armor != 0) {
                $new = (($this->getFregatePlasma() * 451) - $armor) / 451;
                if($new < 0) {
                    $armor = $new * -451;
                    $new = $this->getFregatePlasma();
                } else {
                    $armor = 0;
                    $new = $this->getFregatePlasma() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Frégates a plasma : " . $this->getFregatePlasma() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getCroiser()) {
            if($armor != 0) {
                $new = (($this->getCroiser() * 957) - $armor) / 957;
                if ($new < 0) {
                    $armor = $new * -957;
                    $new = $this->getCroiser();
                } else {
                    $armor = 0;
                    $new = $this->getCroiser() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Croiseurs : " . $this->getCroiser() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getDestroyer()) {
            if($armor != 0) {
                $new = (($this->getDestroyer() * 5176) - $armor) / 5176;
                if ($new < 0) {
                    $armor = $new * -5176;
                    $new = $this->getDestroyer();
                } else {
                    $armor = 0;
                    $new = $this->getDestroyer() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Destroyers : " . $this->getDestroyer() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
        }
        if($this->getIronClad()) {
            if($armor != 0) {
                $new = (($this->getIronClad() * 2415) - $armor) / 2415;
                if ($new < 0) {
                    $new = $this->getIronClad();
                } else {
                    $new = $this->getIronClad() - $new;
                }
            } else {
                $new = 0;
            }
            $ships = $ships . "Cuirassés : " . $this->getIronClad() . " <span class='float-right'>Perte : <span class=\"text-rouge\">" . round($new) . "</span></span><br>";
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
        if($this->getHunterWar()) {
            $ships = $ships . "Chasseurs à plasma : " . $this->getHunterWar() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCorvet()) {
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCorvetLaser()) {
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : Aucune</span><br>";
        }
        if($this->getCorvetWar()) {
            $ships = $ships . "Corvettes Armageddon : " . $this->getCorvetWar() . " <span class='float-right'>Perte : Aucune</span><br>";
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
        if($this->getSonde()) {
            $ships = "<small>Sonde : " . $this->getSonde() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getColonizer()) {
            $ships = $ships . "<small>Colonisateur : " . $this->getColonizer() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getCargoI()) {
            $ships = $ships . "<small>Cargo I : " . $this->getCargoI() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getCargoV()) {
            $ships = $ships . "<small>Cargo V : " . $this->getCargoV() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getCargoX()) {
            $ships = $ships . "<small>Cargo X : " . $this->getCargoX() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getRecycleur()) {
            $ships = $ships . "<small>Recycleur : " . $this->getRecycleur() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getBarge()) {
            $ships = $ships . "<small>Barge : " . $this->getBarge() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getMoonMaker()) {
            $ships = $ships . "<small>Lunar : " . $this->getMoonMaker() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getRadarShip()) {
            $ships = $ships . "<small>Vaisseau radar : " . $this->getRadarShip() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getBrouilleurShip()) {
            $ships = $ships . "<small>Vaisseau brouilleur : " . $this->getBrouilleurShip() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getMotherShip()) {
            $ships = $ships . "<small>Vaisseau mère : " . $this->getMotherShip() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span></small><br>";
        }
        if($this->getHunter()) {
            $ships = $ships . "Chasseurs : " . $this->getHunter() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getHunterHeavy()) {
            $ships = $ships . "Chasseurs lourds : " . $this->getHunterHeavy() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getHunterWar()) {
            $ships = $ships . "Chasseurs à plasma : " . $this->getHunterWar() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCorvet()) {
            $ships = $ships . "Corvettes : " . $this->getCorvet() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCorvetLaser()) {
            $ships = $ships . "Corvettes à laser : " . $this->getCorvetLaser() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
        }
        if($this->getCorvetWar()) {
            $ships = $ships . "Corvettes Armageddon : " . $this->getCorvetWar() . " <span class='float-right'>Perte : " . "<span class=\"text-rouge\">" . "Totale</span></span><br>";
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
                    return null;
                }
            }
        } elseif ($this->getUser() == $this->getPlanet()->getUser()) {
            return null;
        }
        return 'ok';
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
        $barge = $this->getBarge() * 2500;
        $recycleur = $this->getRecycleur() * 10000;
        $cargoI = $this->getCargoI() * 25000;
        $cargoV = $this->getCargoV() * 100000;
        $cargoX = $this->getCargoX() * 250000;

        $nbr = $barge + $recycleur + $cargoI + $cargoV + $cargoX;
        if($this->getMotherShip() == 1) {
            $nbr = $nbr / 0.10;
        }
        return $nbr;
    }

    /**
     * @return int
     */
    public function getArmor(): int
    {
        $hunter = $this->getHunter() * 15;
        $hunterHeavy = $this->getHunterHeavy() * 25;
        $hunterWar = $this->getHunterWar() * 53;
        $corvet = $this->getCorvet() * 74;
        $corvetLaser = $this->getCorvetLaser() * 115;
        $corvetWar = $this->getCorvetWar() * 135;
        $fregate = $this->getFregate() * 168;
        $fregatePlasma = $this->getFregatePlasma() * 451;
        $croiser = $this->getCroiser() * 957;
        $ironClad = $this->getIronClad() * 2415;
        $destroyer = $this->getDestroyer() * 5176;
        if($this->getMotherShip() == 1) {
            $motherShip = 20000;
        } else {
            $motherShip = 0;
        }

        $nbr = $motherShip + $hunterWar + $corvetWar +  $fregate + $hunter + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        if($this->getMotherShip() == 1) {
            $nbr = $nbr / 0.05;
        }
        return $nbr;
    }

    /**
     * @return int
     */
    public function getPlasma(): int
    {
        $hunterWar = $this->getHunterWar() * 5;
        $fregatePlasma = $this->getFregatePlasma() * 20;
        $croiser = $this->getCroiser() * 10;
        $ironClad = $this->getIronClad() * 15;
        $destroyer = $this->getDestroyer() * 2;

        $nbr = $fregatePlasma + $croiser + $ironClad + $destroyer + $hunterWar;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getMissile(): int
    {
        $hunter = $this->getHunter() * 4;
        $hunterHeavy = $this->getHunterHeavy() * 5;
        $hunterWar = $this->getHunterWar() * 1;
        $corvet = $this->getCorvet() * 15;
        $corvetLaser = $this->getCorvetLaser() * 20;
        $corvetWar = $this->getCorvetWar() * 26;
        $fregate = $this->getFregate() * 40;
        $fregatePlasma = $this->getFregatePlasma() * 55;
        $croiser = $this->getCroiser() * 80;
        $ironClad = $this->getIronClad() * 100;
        $destroyer = $this->getDestroyer() * 200;

        $nbr = $hunterWar + $corvetWar + $fregate + $hunter + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
    }

    /**
     * @return int
     */
    public function getShield(): int
    {
        $corvet = $this->getCorvet() * 2;
        $corvetLaser = $this->getCorvetLaser() * 5;
        $corvetWar = $this->getCorvetWar() * 10;
        $fregate = $this->getFregate() * 10;
        $fregatePlasma = $this->getFregatePlasma() * 15;
        $croiser = $this->getCroiser() * 22;
        $ironClad = $this->getIronClad() * 25;
        $destroyer = $this->getDestroyer() * 200;
        $motherShip = $this->getMotherShip() * 5000;

        $nbr = $motherShip + $corvetWar + $fregate + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        if($this->getMotherShip() == 1) {
            $nbr = $nbr / 0.05;
        }
        return $nbr;
    }

    /**
     * @return int
     */
    public function getLaser(): int
    {
        $hunterWar = $this->getHunterWar() * 1;
        $corvetLaser = $this->getCorvetLaser() * 8;
        $corvetWar = $this->getCorvetWar() * 9;
        $fregate = $this->getFregate() * 6;
        $fregatePlasma = $this->getFregatePlasma() * 7;
        $croiser = $this->getCroiser() * 20;
        $ironClad = $this->getIronClad() * 15;
        $destroyer = $this->getDestroyer() * 2;

        $nbr = $hunterWar + $corvetWar + $fregate + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
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
        $moonMaker = $this->getMoonMaker();
        $radarShip = $this->getRadarShip();
        $brouilleurShip = $this->getBrouilleurShip();
        $motherShip = $this->getMotherShip();
        $hunter = $this->getHunter();
        $hunterHeavy = $this->getHunterHeavy();
        $hunterWar = $this->getHunterWar();
        $corvet = $this->getCorvet();
        $corvetLaser = $this->getCorvetLaser();
        $corvetWar = $this->getCorvetWar();
        $fregate = $this->getFregate();
        $fregatePlasma = $this->getFregatePlasma();
        $croiser = $this->getCroiser();
        $ironClad = $this->getIronClad();
        $destroyer = $this->getDestroyer();

        $nbr = $motherShip + $brouilleurShip + $radarShip + $moonMaker + $hunterWar + $corvetWar + $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde + $cargoI + $cargoV + $cargoX + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
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
        $moonMaker = $this->getMoonMaker() * 50000;
        $radarShip = $this->getRadarShip() * 200;
        $brouilleurShip = $this->getBrouilleurShip() * 500;
        $motherShip = $this->getMotherShip() * 20000;
        $hunter = $this->getHunter() * 5;
        $hunterHeavy = $this->getHunterHeavy() * 8;
        $hunterWar = $this->getHunterWar() * 15;
        $corvet = $this->getCorvet() * 25;
        $corvetLaser = $this->getCorvetLaser() * 40;
        $corvetWar = $this->getCorvetWar() * 45;
        $fregate = $this->getFregate() * 60;
        $fregatePlasma = $this->getFregatePlasma() * 150;
        $croiser = $this->getCroiser() * 300;
        $ironClad = $this->getIronClad() * 700;
        $destroyer = $this->getDestroyer() * 1500;

        $nbr = $motherShip + $brouilleurShip + $radarShip + $moonMaker + $hunterWar + $corvetWar + $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde + $cargoI + $cargoV + $cargoX + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        if($this->getMotherShip() == 1) {
            $nbr = $nbr * 0.1;
        }
        return $nbr;
    }

    /**
     * @return float
     */
    public function getSpeed(): float
    {
        if($this->getMoonMaker()) {
            return 5;
        }
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
        if($this->getCorvetLaser() || $this->getMotherShip()) {
            return 1;
        }
        if($this->getCorvet() || $this->getCorvetWar()) {
            return 0.7;
        }
        if($this->getCargoX()) {
            return 0.6;
        }
        if($this->getHunter() || $this->getHunterHeavy() || $this->getHunterWar() || $this->getCargoV()) {
            return 0.5;
        }
        if($this->getCargoI()) {
            return 0.4;
        }
        if($this->getSonde() || $this->getBrouilleurShip() || $this->getRadarShip()) {
            return 0.01;
        }

        return 1;
    }

    /**
     * @param $armor
     */
    public function setFleetWinRatio($armor): void
    {
        if($this->getMotherShip() == 1) {
            $armor = $armor * 0.05;
        }
        if($armor > 0) {
            if ($this->getHunter()) {
                $new = (($this->getHunter() * 15) - $armor) / 15;
                if($new < 0) {
                    $armor = $new * -15;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setHunter(round($new));
            }
            if ($this->getCorvet() && $armor != 0) {
                $new = (($this->getCorvet() * 74) - $armor) / 74;
                if($new < 0) {
                    $armor = $new * -74;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setCorvet(round($new));
            }
            if ($this->getHunterHeavy() && $armor != 0) {
                $new = (($this->getHunterHeavy() * 25) - $armor) / 25;
                if($new < 0) {
                    $armor = $new * -25;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setHunterHeavy(round($new));
            }
            if ($this->getFregate() && $armor != 0) {
                $new = (($this->getFregate() * 168) - $armor) / 168;
                if($new < 0) {
                    $armor = $new * -168;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setFregate(round($new));
            }
            if ($this->getHunterWar() && $armor != 0) {
                $new = (($this->getHunterWar() * 53) - $armor) / 53;
                if($new < 0) {
                    $armor = $new * -53;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setHunterWar(round($new));
            }
            if ($this->getCorvetLaser() && $armor != 0) {
                $new = (($this->getCorvetLaser() * 115) - $armor) / 115;
                if($new < 0) {
                    $armor = $new * -115;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setCorvetLaser(round($new));
            }
            if ($this->getCorvetWar() && $armor != 0) {
                $new = (($this->getCorvetWar() * 135) - $armor) / 135;
                if($new < 0) {
                    $armor = $new * -135;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setCorvetWar(round($new));
            }
            if ($this->getFregatePlasma() && $armor != 0) {
                $new = (($this->getFregatePlasma() * 451) - $armor) / 451;
                if($new < 0) {
                    $armor = $new * -451;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setFregatePlasma(round($new));
            }
            if ($this->getCroiser() && $armor != 0) {
                $new = (($this->getCroiser() * 957) - $armor) / 957;
                if($new < 0) {
                    $armor = $new * -957;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setCroiser(round($new));
            }
            if ($this->getDestroyer() && $armor != 0) {
                $new = (($this->getDestroyer() * 5176) - $armor) / 5176;
                if($new < 0) {
                    $armor = $new * -5176;
                } else {
                    $armor = 0;
                }
                if($new < 0) {
                    $new = 0;
                }
                $this->setDestroyer(round($new));
            }
            if ($this->getIronClad() && $armor != 0) {
                $new = (($this->getIronClad() * 2415) - $armor) / 2415;
                if($new < 0) {
                    $new = 0;
                }
                $this->setIronClad(round($new));
            }
        }
    }

    /**
     * @return string
     */
    public function getFleetsColor($user): string
    {
        $color = 'pp-enemy';
        if ($this->getUser() == $user) {
            return 'pp-mine';
        }
        if($this->getUser()->getAlly() == $user->getAlly() && $color != 'pp-mine' && $user->getAlly()) {
            return 'pp-ally';
        }
        if ($this->getUser()->getAlly() && $user->getAlly()) {
            if (count($this->getUser()->getAlly()->getAllieds()) > 0) {
                foreach($this->getUser()->getAlly()->getAllieds() as $allied) {
                    if($allied->getAllyTag() == $user->getAlly()->getSigle() && $allied->getAccepted() == 1) {
                        return 'pp-ally';
                    }
                }
            }
            if (count($this->getUser()->getAlly()->getPeaces()) > 0) {
                foreach($this->getUser()->getAlly()->getPeaces() as $peace) {
                    if($peace->getAllyTag() == $user->getAlly()->getSigle() && $peace->getAccepted() == 1) {
                        return 'pp-peace';
                    }
                }
            }
        }
        return $color;
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

    /**
     * @return mixed
     */
    public function getHunterWar()
    {
        return $this->hunterWar;
    }

    /**
     * @param mixed $hunterWar
     */
    public function setHunterWar($hunterWar): void
    {
        $this->hunterWar = $hunterWar;
    }

    /**
     * @return mixed
     */
    public function getCorvetWar()
    {
        return $this->corvetWar;
    }

    /**
     * @param mixed $corvetWar
     */
    public function setCorvetWar($corvetWar): void
    {
        $this->corvetWar = $corvetWar;
    }

    /**
     * @return mixed
     */
    public function getCommander()
    {
        return $this->commander;
    }

    /**
     * @param mixed $commander
     */
    public function setCommander($commander): void
    {
        $this->commander = $commander;
    }

    /**
     * @return mixed
     */
    public function getMoonMaker()
    {
        return $this->moonMaker;
    }

    /**
     * @param mixed $moonMaker
     */
    public function setMoonMaker($moonMaker): void
    {
        $this->moonMaker = $moonMaker;
    }

    /**
     * @return mixed
     */
    public function getRadarShip()
    {
        return $this->radarShip;
    }

    /**
     * @param mixed $radarShip
     */
    public function setRadarShip($radarShip): void
    {
        $this->radarShip = $radarShip;
    }

    /**
     * @return mixed
     */
    public function getBrouilleurShip()
    {
        return $this->brouilleurShip;
    }

    /**
     * @param mixed $brouilleurShip
     */
    public function setBrouilleurShip($brouilleurShip): void
    {
        $this->brouilleurShip = $brouilleurShip;
    }

    /**
     * @return mixed
     */
    public function getMotherShip()
    {
        return $this->motherShip;
    }

    /**
     * @param mixed $motherShip
     */
    public function setMotherShip($motherShip): void
    {
        $this->motherShip = $motherShip;
    }

    /**
     * @return mixed
     */
    public function getCancelFlight()
    {
        return $this->cancelFlight;
    }

    /**
     * @param mixed $cancelFlight
     */
    public function setCancelFlight($cancelFlight): void
    {
        $this->cancelFlight = $cancelFlight;
    }
}
