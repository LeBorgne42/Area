<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

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
    protected $niobium = 1500;

    /**
     * @ORM\Column(name="water",type="decimal", precision=28, scale=5)
     */
    protected $water = 750;

    /**
     * @ORM\OneToOne(targetEntity="Building", inversedBy="planet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="building_id", referencedColumnName="id")
     */
    protected $building;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $fleets;

    /**
     * @ORM\OneToOne(targetEntity="Ship", mappedBy="planet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ship_id", referencedColumnName="id")
     */
    protected $ship;

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
     * @Assert\NotBlank(message = "required")
     */
    protected $imageName;

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
    public function remove(\App\Entity\Fleet $fleet)
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
    public function getBuilding()
    {
        return $this->building;
    }

    /**
     * @param mixed $building
     */
    public function setBuilding($building): void
    {
        $this->building = $building;
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

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getId()
    {
        return $this->id;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function getShipOn()
    {
        $fregate = $this->ship->getFregate()->getAmount();
        $hunter = $this->ship->getHunter()->getAmount();
        $sonde = $this->ship->getSonde()->getAmount();
        $colonizer = $this->ship->getColonizer()->getAmount();
        $recycleur = $this->ship->getRecycleur()->getAmount();
        $barge = $this->ship->getBarge()->getAmount();

        $nbr = $fregate + $hunter + $sonde + $colonizer + $recycleur + $barge;

        return $nbr;
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
    public function getNbrFleets()
    {
        $nbr = count($this->fleets);

        return $nbr;
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
     * Set name
     *
     * @param string $name
     *
     * @return Planet
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
