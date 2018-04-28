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
     * @ORM\Column(name="name",type="string", length=15, nullable=true)
     */
    protected $name = 'Inhabitée';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="planets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="niobium",type="decimal", precision=28, scale=5)
     */
    protected $niobium = 3000;

    /**
     * @ORM\Column(name="water",type="decimal", precision=28, scale=5)
     */
    protected $water = 1500;

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
     * @ORM\Column(name="workerProduction",type="decimal", precision=28, scale=5)
     */
    protected $workerProduction = 1.2;

    /**
     * @ORM\Column(name="soldierMax",type="integer")
     */
    protected $soldierMax = 2500;

    /**
     * @ORM\Column(name="nbProduction",type="decimal", precision=28, scale=5)
     */
    protected $nbProduction = 5.2;

    /**
     * @ORM\Column(name="wtProduction",type="decimal", precision=28, scale=5)
     */
    protected $wtProduction = 4.3;

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
     * @ORM\Column(name="miner",type="integer", nullable=true)
     */
    protected $miner = 0;

    /**
     * @ORM\Column(name="extractor",type="integer", nullable=true)
     */
    protected $extractor = 0;

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
     * @Assert\NotBlank(message = "required")
     */
    protected $empty = false;

    /**
     * @ORM\Column(name="cdr",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $cdr = false;

    /**
     * @ORM\Column(name="merchant",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $merchant = false;

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
     * @return mixed
     */
    public function getShipOn()
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
    public function getFleetNoFriends()
    {
        $fullFleet = [];
        $x = 0;
        foreach($this->fleets as $fleet) {
            if($fleet->getUser() == $this->user || $fleet->getUser()->getAlly() == $this->user->getAlly()) {
            } else {
                $fullFleet[$x] = $fleet;
            }
        }
        return $fullFleet;
    }

    /**
     * @return mixed
     */
    public function getBargeSignature()
    {
        return $this->getBarge() * 50;
    }

    /**
     * @return mixed
     */
    public function getColonizerSignature()
    {
        return $this->getColonizer() * 200;
    }

    /**
     * @return mixed
     */
    public function getFregateSignature()
    {
        return $this->getFregate() * 85;
    }

    /**
     * @return mixed
     */
    public function getHunterSignature()
    {
        return $this->getHunter() * 3;
    }

    /**
     * @return mixed
     */
    public function getRecycleurSignature()
    {
        return $this->getRecycleur() * 80;
    }

    /**
     * @return mixed
     */
    public function getNbrFleets()
    {
        $nbr = count($this->fleets);
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
}