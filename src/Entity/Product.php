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
     * @ORM\Column(name="productAt",type="datetime")
     */
    protected $productAt;

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
