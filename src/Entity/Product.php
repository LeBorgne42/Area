<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="product")
 * @ORM\Entity
 */
class Product
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Planet", inversedBy="product", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    protected $planet;

    /**
     * @ORM\Column(name="soldier",type="integer")
     */
    protected $soldier;

    /**
     * @ORM\Column(name="tank",type="smallint")
     */
    protected $tank;

    /**
     * @ORM\Column(name="scientist",type="smallint")
     */
    protected $scientist;

    /**
     * @ORM\Column(name="sonde",type="integer")
     */
    protected $sonde;

    /**
     * @ORM\Column(name="cargoI",type="integer")
     */
    protected $cargoI;

    /**
     * @ORM\Column(name="cargoV",type="integer")
     */
    protected $cargoV;

    /**
     * @ORM\Column(name="cargoX",type="integer")
     */
    protected $cargoX;

    /**
     * @ORM\Column(name="colonizer",type="smallint")
     */
    protected $colonizer;

    /**
     * @ORM\Column(name="recycleur",type="integer")
     */
    protected $recycleur;

    /**
     * @ORM\Column(name="barge",type="integer")
     */
    protected $barge;

    /**
     * @ORM\Column(name="moonMaker",type="smallint")
     */
    protected $moonMaker;

    /**
     * @ORM\Column(name="radarShip",type="integer")
     */
    protected $radarShip;

    /**
     * @ORM\Column(name="brouilleurShip",type="integer")
     */
    protected $brouilleurShip;

    /**
     * @ORM\Column(name="motherShip",type="smallint")
     */
    protected $motherShip;

    /**
     * @ORM\Column(name="hunter",type="integer")
     */
    protected $hunter;

    /**
     * @ORM\Column(name="hunterHeavy",type="integer")
     */
    protected $hunterHeavy;

    /**
     * @ORM\Column(name="hunterWar",type="integer")
     */
    protected $hunterWar;

    /**
     * @ORM\Column(name="corvet",type="integer")
     */
    protected $corvet;

    /**
     * @ORM\Column(name="corvetLaser",type="integer")
     */
    protected $corvetLaser;

    /**
     * @ORM\Column(name="corvetWar",type="integer")
     */
    protected $corvetWar;

    /**
     * @ORM\Column(name="fregate",type="integer")
     */
    protected $fregate;

    /**
     * @ORM\Column(name="fregatePlasma",type="integer")
     */
    protected $fregatePlasma;

    /**
     * @ORM\Column(name="croiser",type="integer")
     */
    protected $croiser;

    /**
     * @ORM\Column(name="ironClad",type="integer")
     */
    protected $ironClad;

    /**
     * @ORM\Column(name="destroyer",type="integer")
     */
    protected $destroyer;

    /**
     * @ORM\Column(name="productAt",type="datetime")
     */
    protected $productAt;
    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->destroyer = 0;
        $this->ironClad = 0;
        $this->croiser = 0;
        $this->fregate = 0;
        $this->fregatePlasma = 0;
        $this->corvet = 0;
        $this->corvetLaser = 0;
        $this->corvetWar = 0;
        $this->hunter = 0;
        $this->hunterHeavy = 0;
        $this->hunterWar = 0;
        $this->moonMaker = 0;
        $this->motherShip = 0;
        $this->radarShip = 0;
        $this->brouilleurShip = 0;
        $this->barge = 0;
        $this->recycleur = 0;
        $this->cargoI = 0;
        $this->cargoV = 0;
        $this->cargoX = 0;
        $this->colonizer = 0;
        $this->sonde = 0;
        $this->soldier = 0;
        $this->tank = 0;
        $this->scientist = 0;
    }

    /**
     * @return mixed
     */
    public function getShips()
    {
        $return = [];
        if ($this->sonde > 0) {
            $return[] = [number_format($this->sonde), 'sonde'];
        }
        if ($this->colonizer > 0) {
            $return[] = [number_format($this->colonizer), 'colonizer'];
        }
        if ($this->cargoI > 0) {
            $return[] = [number_format($this->cargoI), 'cargoI'];
        }
        if ($this->cargoV > 0) {
            $return[] = [number_format($this->cargoV), 'cargoV'];
        }
        if ($this->cargoX > 0) {
            $return[] = [number_format($this->cargoX), 'cargoX'];
        }
        if ($this->barge > 0) {
            $return[] = [number_format($this->barge), 'barges'];
        }
        if ($this->recycleur > 0) {
            $return[] = [number_format($this->recycleur), 'recycleurs'];
        }
        if ($this->radarShip > 0) {
            $return[] = [number_format($this->radarShip), 'radarShip'];
        }
        if ($this->brouilleurShip > 0) {
            $return[] = [number_format($this->brouilleurShip), 'brouilleurShip'];
        }
        if ($this->moonMaker > 0) {
            $return[] = [number_format($this->moonMaker), 'moonMaker'];
        }
        if ($this->motherShip > 0) {
            $return[] = [number_format($this->motherShip), 'motherShip'];
        }
        if ($this->hunter > 0) {
            $return[] = [number_format($this->hunter), 'hunter'];
        }
        if ($this->hunterHeavy > 0) {
            $return[] = [number_format($this->hunterHeavy), 'hunterHeavy'];
        }
        if ($this->hunterWar > 0) {
            $return[] = [number_format($this->hunterWar), 'hunterWar'];
        }
        if ($this->corvet > 0) {
            $return[] = [number_format($this->corvet), 'corvet'];
        }
        if ($this->corvetLaser > 0) {
            $return[] = [number_format($this->corvetLaser), 'corvetLaser'];
        }
        if ($this->corvetWar > 0) {
            $return[] = [number_format($this->corvetWar), 'corvetWar'];
        }
        if ($this->fregate > 0) {
            $return[] = [number_format($this->fregate), 'fregate'];
        }
        if ($this->fregatePlasma > 0) {
            $return[] = [number_format($this->fregatePlasma), 'fregatePlasma'];
        }
        if ($this->croiser > 0) {
            $return[] = [number_format($this->croiser), 'croiser'];
        }
        if ($this->ironClad > 0) {
            $return[] = [number_format($this->ironClad), 'ironClad'];
        }
        if ($this->destroyer > 0) {
            $return[] = [number_format($this->destroyer), 'destroyer'];
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
        $radarShip = $this->getRadarShip() * 500;
        $brouilleurShip = $this->getBrouilleurShip() * 1000;
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

        $nbr = $corvetWar + $hunterWar + $motherShip + $brouilleurShip + $radarShip + $radarShip + $moonMaker + $fregate + $colonizer + $barge + $hunter + $recycleur + $sonde + $cargoI + $cargoV + $cargoX + $hunterHeavy + $corvet + $corvetLaser + $fregatePlasma + $croiser + $ironClad + $destroyer ;
        return $nbr;
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
    public function getProductAt()
    {
        return $this->productAt;
    }

    /**
     * @param mixed $productAt
     */
    public function setProductAt($productAt): void
    {
        $this->productAt = $productAt;
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
    public function getTank()
    {
        return $this->tank;
    }

    /**
     * @param mixed $tank
     */
    public function setTank($tank): void
    {
        $this->tank = $tank;
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
}
