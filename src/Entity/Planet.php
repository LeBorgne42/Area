<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Table(name="planet")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Planet
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="name",type="string", length=20, nullable=true)
     */
    protected $name = 'Inhabitée';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="planets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Commander", inversedBy="planet", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="commander_id", referencedColumnName="id")
     */
    protected $commander;

    /**
     * @ORM\Column(name="niobium",type="decimal", precision=28, scale=5)
     */
    protected $niobium = 7500;

    /**
     * @ORM\Column(name="water",type="decimal", precision=28, scale=5)
     */
    protected $water = 5000;

    /**
     * @ORM\Column(name="nbCdr",type="bigint", nullable=true)
     */
    protected $nbCdr = 0;

    /**
     * @ORM\Column(name="wtCdr",type="bigint", nullable=true)
     */
    protected $wtCdr = 0;

    /**
     * @ORM\Column(name="shipProduction",type="decimal", precision=28, scale=5)
     */
    protected $shipProduction = 1;

    /**
     * @ORM\OneToOne(targetEntity="Product", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $product;

    /**
     * @ORM\Column(name="workerProduction",type="integer")
     */
    protected $workerProduction = 6000;

    /**
     * @ORM\Column(name="niobiumMax",type="integer")
     */
    protected $niobiumMax = 1000000;

    /**
     * @ORM\Column(name="waterMax",type="integer")
     */
    protected $waterMax = 1000000;

    /**
     * @ORM\Column(name="soldierMax",type="integer")
     */
    protected $soldierMax = 2500;

    /**
     * @ORM\Column(name="scientistMax",type="integer")
     */
    protected $scientistMax = 500;

    /**
     * @ORM\Column(name="workerMax",type="integer")
     */
    protected $workerMax = 125000;

    /**
     * @ORM\Column(name="nbProduction",type="decimal", precision=28, scale=5)
     */
    protected $nbProduction = 6;

    /**
     * @ORM\Column(name="wtProduction",type="decimal", precision=28, scale=5)
     */
    protected $wtProduction = 5;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $fleets;

    /**
     * @ORM\Column(name="construct",type="string", nullable=true)
     */
    protected $construct;

    /**
     * @ORM\Column(name="constructAt",type="datetime", nullable=true)
     */
    protected $constructAt;

    /**
     * @ORM\Column(name="scientistAt",type="datetime", nullable=true)
     */
    protected $scientistAt;

    /**
     * @ORM\Column(name="scientistAtNbr",type="integer", nullable=true)
     */
    protected $scientistAtNbr;

    /**
     * @ORM\Column(name="soldierAt",type="datetime", nullable=true)
     */
    protected $soldierAt;

    /**
     * @ORM\Column(name="soldierAtNbr",type="integer", nullable=true)
     */
    protected $soldierAtNbr;

    /**
     * @ORM\Column(name="miner",type="integer", nullable=true)
     */
    protected $miner = 0;

    /**
     * @ORM\Column(name="niobiumStock",type="integer", nullable=true)
     */
    protected $niobiumStock = 0;

    /**
     * @ORM\Column(name="extractor",type="integer", nullable=true)
     */
    protected $extractor = 0;

    /**
     * @ORM\Column(name="waterStock",type="integer", nullable=true)
     */
    protected $waterStock = 0;

    /**
     * @ORM\Column(name="spaceShip",type="integer", nullable=true)
     */
    protected $spaceShip = 0;

    /**
     * @ORM\Column(name="centerSearch",type="integer", nullable=true)
     */
    protected $centerSearch;

    /**
     * @ORM\Column(name="metropole",type="integer", nullable=true)
     */
    protected $metropole = 0;

    /**
     * @ORM\Column(name="city",type="integer", nullable=true)
     */
    protected $city = 0;

    /**
     * @ORM\Column(name="caserne",type="integer", nullable=true)
     */
    protected $caserne = 0;

    /**
     * @ORM\Column(name="radar",type="integer", nullable=true)
     */
    protected $radar = 0;

    /**
     * @ORM\Column(name="skyRadar",type="integer", nullable=true)
     */
    protected $skyRadar = 0;

    /**
     * @ORM\Column(name="skyBrouilleur",type="integer", nullable=true)
     */
    protected $skyBrouilleur = 0;

    /**
     * @ORM\Column(name="lightUsine",type="integer", nullable=true)
     */
    protected $lightUsine = 0;

    /**
     * @ORM\Column(name="heavyUsine",type="integer", nullable=true)
     */
    protected $heavyUsine = 0;

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
    protected $soldier = 50;

    /**
     * @ORM\Column(name="worker",type="integer")
     */
    protected $worker = 10000;

    /**
     * @ORM\Column(name="scientist",type="integer", nullable=true)
     */
    protected $scientist = 100;

    /**
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="planets", fetch="EXTRA_LAZY")
     */
    protected $sector;

    /**
     * @ORM\Column(name="position",type="integer")
     * @Assert\NotBlank(message = "required")
     */
    protected $position;

    /**
     * @ORM\Column(name="ground",type="integer", nullable=true)
     */
    protected $ground;

    /**
     * @ORM\Column(name="groundPlace",type="integer", nullable=true)
     */
    protected $groundPlace = 0;

    /**
     * @ORM\Column(name="sky",type="integer", nullable=true)
     */
    protected $sky;

    /**
     * @ORM\Column(name="skyPlace",type="integer", nullable=true)
     */
    protected $skyPlace = 0;

    /**
     * @ORM\Column(name="empty",type="boolean")
     */
    protected $empty = false;

    /**
     * @ORM\Column(name="cdr",type="boolean")
     */
    protected $cdr = false;

    /**
     * @ORM\Column(name="merchant",type="boolean")
     */
    protected $merchant = false;

    /**
     * @ORM\Column(name="moon",type="boolean")
     */
    protected $moon = false;

    /**
     * @ORM\Column(name="radarAt",type="datetime", nullable=true)
     */
    protected $radarAt = null;

    /**
     * @ORM\Column(name="brouilleurAt",type="datetime", nullable=true)
     */
    protected $brouilleurAt = null;

    /**
     * @Assert\File(
     *     maxSize="400k",
     *     mimeTypes={"image/png", "image/jpeg", "image/bmp"}
     * )
     * @Vich\UploadableField(mapping="planet_img", fileNameProperty="imageName" )
     *
     * @var File
     */
    private $imageFile= null;

    /**
     * @ORM\Column(name="imageName",type="string", length=20, nullable=true)
     */
    protected $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    public function __construct()
    {
        $this->fleets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the  update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile $image
     */
    public function setImageFile(?File $image = null): void
    {
        $this->imageFile = $image;

        if (null !== $image) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getShipOn(): int
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
     * @return int
     */
    public function getBuildingPoint(): int
    {
        $extractor = $this->getExtractor() * 100;
        $miner = $this->getMiner() * 150;
        $niobiumStock = $this->getNiobiumStock() * 400;
        $waterStock = $this->getWaterStock() * 450;
        $caserne = $this->getCaserne() * 250;
        $center = $this->getCenterSearch() * 300;
        $city = $this->getCity() * 650;
        $metropole = $this->getMetropole() * 1200;
        $light = $this->getLightUsine() * 500;
        $heavy = $this->getHeavyUsine() * 1500;
        $space = $this->getSpaceShip() * 300;
        $radar = $this->getRadar() * 80;
        $skyr = $this->getSkyRadar() * 600;
        $brouilleur = $this->getSkyBrouilleur() * 1000;

        $nbr = $extractor + $niobiumStock + $waterStock + $miner + $caserne + $center + $city + $metropole + $light + $heavy + $space + $radar + $skyr + $brouilleur;
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getFleetNoFriends($user)
    {
        $fullFleet = [];
        $x = 0;
        foreach($this->fleets as $fleet) {
            if($fleet->getUser() == $this->user || $fleet->getUser()->getAlly() == $user->getAlly()) {
            } else {
                $fullFleet[$x] = $fleet;
            }
        }
        return $fullFleet;
    }

    /**
     * @return int
     */
    public function getFleetWithRec(): int
    {
        $nbr = 0;
        foreach($this->fleets as $fleet) {
            if($fleet->getRecycleur() > 0) {
                $nbr++;
            }
        }

        return $nbr;
    }

    /**
     * @return string
     */
    public function getFleetsColor($user): string
    {
        $color = 'pp-enemy';
        foreach($this->fleets as $fleet) {
            if($fleet->getUser()->getAlly() == $user->getAlly() && $color != 'pp-mine') {
                $color = 'pp-ally';
            }
            if ($fleet->getUser() == $user) {
                $color = 'pp-mine';
            }
            if($fleet->getUser()->getAlly() != $user->getAlly() && $fleet->getUser() != $user) {
                $color = 'pp-enemy';
                return $color;
            }
        }
        return $color;
    }

    /**
     * @return int
     */
    public function getFleetsAbandon($user): int
    {
        $planete = 0;
        foreach($this->fleets as $fleet) {
            if($fleet->getPlanete()) {
                $planete = 0;
            } else {
                if($fleet->getUser() != $user) {
                    $planete = 1;
                    break;
                }
            }
        }
        return $planete;
    }

    /**
     * @return mixed
     */
    public function getPlanetAlliance()
    {
        if ($this->getUser()) {
            return $this->getUser()->getAlly();
        } else {
            return null;
        }
    }

    /**
     * @return mixed
     */
    public function getOurFleet($user)
    {

        foreach($this->getFleets() as $fleet) {
            if ($fleet->getUser() == $user && $fleet->getFlightTime() == null) {
                return 'hello';
            }
        }
        return null;
    }

    /**
     * @return int
     */
    public function getNbrFleets(): int
    {
        $nbr = 0;
        foreach($this->fleets as $fleet) {
            if ($fleet->getFlightTime() == null ) {
                $nbr++;
            }
        }
        return $nbr;
    }

    /**
     * Add fleet
     *
     * @param \App\Entity\Fleet $fleet
     *
     * @return Planet
     */
    public function addFleet(\App\Entity\Fleet $fleet)
    {
        $this->fleets[] = $fleet;

        return $this;
    }

    /**
     * Remove fleet
     *
     * @param \App\Entity\Fleet $fleet
     */
    public function removeFleet(\App\Entity\Fleet $fleet)
    {
        $this->fleets->removeElement($fleet);
    }

    /**
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \App\Entity\User $user
     * @return Planet
     */
    public function setUser(\App\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
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
    public function getFleets()
    {
        return $this->fleets;
    }

    /**
     * @param mixed $fleets
     */
    public function setFleets($fleets): void
    {
        $this->fleets = $fleets;
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
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
    }

    /**
     * @return mixed
     */
    public function getGround()
    {
        return $this->ground;
    }

    /**
     * @param mixed $ground
     */
    public function setGround($ground): void
    {
        $this->ground = $ground;
    }

    /**
     * @return mixed
     */
    public function getGroundPlace()
    {
        return $this->groundPlace;
    }

    /**
     * @param mixed $groundPlace
     */
    public function setGroundPlace($groundPlace): void
    {
        $this->groundPlace = $groundPlace;
    }

    /**
     * @return mixed
     */
    public function getSky()
    {
        return $this->sky;
    }

    /**
     * @param mixed $sky
     */
    public function setSky($sky): void
    {
        $this->sky = $sky;
    }

    /**
     * @return mixed
     */
    public function getSkyPlace()
    {
        return $this->skyPlace;
    }

    /**
     * @param mixed $skyPlace
     */
    public function setSkyPlace($skyPlace): void
    {
        $this->skyPlace = $skyPlace;
    }

    /**
     * @return mixed
     */
    public function getEmpty()
    {
        return $this->empty;
    }

    /**
     * @param mixed $empty
     */
    public function setEmpty($empty): void
    {
        $this->empty = $empty;
    }

    /**
     * @return mixed
     */
    public function getCdr()
    {
        return $this->cdr;
    }

    /**
     * @param mixed $cdr
     */
    public function setCdr($cdr): void
    {
        $this->cdr = $cdr;
    }

    /**
     * @return mixed
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @param mixed $merchant
     */
    public function setMerchant($merchant): void
    {
        $this->merchant = $merchant;
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
    public function getMiner()
    {
        return $this->miner;
    }

    /**
     * @param mixed $miner
     */
    public function setMiner($miner): void
    {
        $this->miner = $miner;
    }

    /**
     * @return mixed
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * @param mixed $extractor
     */
    public function setExtractor($extractor): void
    {
        $this->extractor = $extractor;
    }

    /**
     * @return mixed
     */
    public function getSpaceShip()
    {
        return $this->spaceShip;
    }

    /**
     * @param mixed $spaceShip
     */
    public function setSpaceShip($spaceShip): void
    {
        $this->spaceShip = $spaceShip;
    }

    /**
     * @return mixed
     */
    public function getCenterSearch()
    {
        return $this->centerSearch;
    }

    /**
     * @param mixed $centerSearch
     */
    public function setCenterSearch($centerSearch): void
    {
        $this->centerSearch = $centerSearch;
    }

    /**
     * @return mixed
     */
    public function getMetropole()
    {
        return $this->metropole;
    }

    /**
     * @param mixed $metropole
     */
    public function setMetropole($metropole): void
    {
        $this->metropole = $metropole;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getCaserne()
    {
        return $this->caserne;
    }

    /**
     * @param mixed $caserne
     */
    public function setCaserne($caserne): void
    {
        $this->caserne = $caserne;
    }

    /**
     * @return mixed
     */
    public function getRadar()
    {
        return $this->radar;
    }

    /**
     * @param mixed $radar
     */
    public function setRadar($radar): void
    {
        $this->radar = $radar;
    }

    /**
     * @return mixed
     */
    public function getSkyRadar()
    {
        return $this->skyRadar;
    }

    /**
     * @param mixed $skyRadar
     */
    public function setSkyRadar($skyRadar): void
    {
        $this->skyRadar = $skyRadar;
    }

    /**
     * @return mixed
     */
    public function getSkyBrouilleur()
    {
        return $this->skyBrouilleur;
    }

    /**
     * @param mixed $skyBrouilleur
     */
    public function setSkyBrouilleur($skyBrouilleur): void
    {
        $this->skyBrouilleur = $skyBrouilleur;
    }

    /**
     * @return mixed
     */
    public function getLightUsine()
    {
        return $this->lightUsine;
    }

    /**
     * @param mixed $lightUsine
     */
    public function setLightUsine($lightUsine): void
    {
        $this->lightUsine = $lightUsine;
    }

    /**
     * @return mixed
     */
    public function getHeavyUsine()
    {
        return $this->heavyUsine;
    }

    /**
     * @param mixed $heavyUsine
     */
    public function setHeavyUsine($heavyUsine): void
    {
        $this->heavyUsine = $heavyUsine;
    }

    /**
     * @return mixed
     */
    public function getShipProduction()
    {
        return $this->shipProduction;
    }

    /**
     * @param mixed $shipProduction
     */
    public function setShipProduction($shipProduction): void
    {
        $this->shipProduction = $shipProduction;
    }

    /**
     * @return mixed
     */
    public function getWorkerProduction()
    {
        return $this->workerProduction;
    }

    /**
     * @param mixed $workerProduction
     */
    public function setWorkerProduction($workerProduction): void
    {
        $this->workerProduction = $workerProduction;
    }

    /**
     * @return mixed
     */
    public function getConstruct()
    {
        return $this->construct;
    }

    /**
     * @param mixed $construct
     */
    public function setConstruct($construct): void
    {
        $this->construct = $construct;
    }

    /**
     * @return mixed
     */
    public function getConstructAt()
    {
        return $this->constructAt;
    }

    /**
     * @param mixed $constructAt
     */
    public function setConstructAt($constructAt): void
    {
        $this->constructAt = $constructAt;
    }

    /**
     * @return mixed
     */
    public function getNbProduction()
    {
        return $this->nbProduction;
    }

    /**
     * @param mixed $nbProduction
     */
    public function setNbProduction($nbProduction): void
    {
        $this->nbProduction = $nbProduction;
    }

    /**
     * @return mixed
     */
    public function getWtProduction()
    {
        return $this->wtProduction;
    }

    /**
     * @param mixed $wtProduction
     */
    public function setWtProduction($wtProduction): void
    {
        $this->wtProduction = $wtProduction;
    }

    /**
     * @return mixed
     */
    public function getNbCdr()
    {
        return $this->nbCdr;
    }

    /**
     * @param mixed $nbCdr
     */
    public function setNbCdr($nbCdr): void
    {
        $this->nbCdr = $nbCdr;
    }

    /**
     * @return mixed
     */
    public function getWtCdr()
    {
        return $this->wtCdr;
    }

    /**
     * @param mixed $wtCdr
     */
    public function setWtCdr($wtCdr): void
    {
        $this->wtCdr = $wtCdr;
    }

    /**
     * @return mixed
     */
    public function getSoldierMax()
    {
        return $this->soldierMax;
    }

    /**
     * @param mixed $soldierMax
     */
    public function setSoldierMax($soldierMax): void
    {
        $this->soldierMax = $soldierMax;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
    public function getScientistMax()
    {
        return $this->scientistMax;
    }

    /**
     * @param mixed $scientistMax
     */
    public function setScientistMax($scientistMax): void
    {
        $this->scientistMax = $scientistMax;
    }

    /**
     * @return mixed
     */
    public function getWorkerMax()
    {
        return $this->workerMax;
    }

    /**
     * @param mixed $workerMax
     */
    public function setWorkerMax($workerMax): void
    {
        $this->workerMax = $workerMax;
    }

    /**
     * @return mixed
     */
    public function getScientistAt()
    {
        return $this->scientistAt;
    }

    /**
     * @param mixed $scientistAt
     */
    public function setScientistAt($scientistAt): void
    {
        $this->scientistAt = $scientistAt;
    }

    /**
     * @return mixed
     */
    public function getSoldierAt()
    {
        return $this->soldierAt;
    }

    /**
     * @param mixed $soldierAt
     */
    public function setSoldierAt($soldierAt): void
    {
        $this->soldierAt = $soldierAt;
    }

    /**
     * @return mixed
     */
    public function getScientistAtNbr()
    {
        return $this->scientistAtNbr;
    }

    /**
     * @param mixed $scientistAtNbr
     */
    public function setScientistAtNbr($scientistAtNbr): void
    {
        $this->scientistAtNbr = $scientistAtNbr;
    }

    /**
     * @return mixed
     */
    public function getSoldierAtNbr()
    {
        return $this->soldierAtNbr;
    }

    /**
     * @param mixed $soldierAtNbr
     */
    public function setSoldierAtNbr($soldierAtNbr): void
    {
        $this->soldierAtNbr = $soldierAtNbr;
    }

    /**
     * @return mixed
     */
    public function getNiobiumMax()
    {
        return $this->niobiumMax;
    }

    /**
     * @param mixed $niobiumMax
     */
    public function setNiobiumMax($niobiumMax): void
    {
        $this->niobiumMax = $niobiumMax;
    }

    /**
     * @return mixed
     */
    public function getWaterMax()
    {
        return $this->waterMax;
    }

    /**
     * @param mixed $waterMax
     */
    public function setWaterMax($waterMax): void
    {
        $this->waterMax = $waterMax;
    }

    /**
     * @return mixed
     */
    public function getNiobiumStock()
    {
        return $this->niobiumStock;
    }

    /**
     * @param mixed $niobiumStock
     */
    public function setNiobiumStock($niobiumStock): void
    {
        $this->niobiumStock = $niobiumStock;
    }

    /**
     * @return mixed
     */
    public function getWaterStock()
    {
        return $this->waterStock;
    }

    /**
     * @param mixed $waterStock
     */
    public function setWaterStock($waterStock): void
    {
        $this->waterStock = $waterStock;
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param mixed $product
     */
    public function setProduct($product): void
    {
        $this->product = $product;
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
    public function getRadarAt()
    {
        return $this->radarAt;
    }

    /**
     * @param mixed $radarAt
     */
    public function setRadarAt($radarAt): void
    {
        $this->radarAt = $radarAt;
    }

    /**
     * @return mixed
     */
    public function getBrouilleurAt()
    {
        return $this->brouilleurAt;
    }

    /**
     * @param mixed $brouilleurAt
     */
    public function setBrouilleurAt($brouilleurAt): void
    {
        $this->brouilleurAt = $brouilleurAt;
    }

    /**
     * @return mixed
     */
    public function getMoon()
    {
        return $this->moon;
    }

    /**
     * @param mixed $moon
     */
    public function setMoon($moon): void
    {
        $this->moon = $moon;
    }
}