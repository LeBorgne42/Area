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
     * @ORM\Column(name="name",type="string", length=10)
     */
    protected $name = 'Inhabité';

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="planets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Water", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $water;

    /**
     * @ORM\OneToOne(targetEntity="Niobium", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $niobium;

    /**
     * @ORM\OneToMany(targetEntity="Building", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $buildings;

    /**
     * @ORM\OneToOne(targetEntity="Orbite", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $orbite;

    /**
     * @ORM\OneToMany(targetEntity="Human", mappedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $humans;

    /**
     * @ORM\ManyToOne(targetEntity="Sector", inversedBy="planet", fetch="EXTRA_LAZY")
     */
    protected $sector;

    /**
     * @ORM\Column(name="position",type="integer")
     * @Assert\NotBlank(message = "required")
     */
    protected $position;

    /**
     * @Assert\File(
     *     maxSize="400k",
     *     mimeTypes={"image/png", "image/jpeg", "image/bmp"}
     * )
     * @Vich\UploadableField(mapping="user_img", fileNameProperty="imageName", size="imageSize" )
     *
     * @var File
     */
    private $imageFile= null;

    /**
     * @ORM\Column(name="imageName",type="string", length=20)
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
    public function getBuildings()
    {
        return $this->buildings;
    }

    /**
     * @param mixed $buildings
     */
    public function setBuildings($buildings): void
    {
        $this->buildings = $buildings;
    }

    /**
     * @return mixed
     */
    public function getOrbite()
    {
        return $this->orbite;
    }

    /**
     * @param mixed $orbite
     */
    public function setOrbite($orbite): void
    {
        $this->orbite = $orbite;
    }

    /**
     * @return mixed
     */
    public function getHumans()
    {
        return $this->humans;
    }

    /**
     * @param mixed $humans
     */
    public function setHumans($humans): void
    {
        $this->humans = $humans;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->username;
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
