<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="ally")
 * @ORM\Entity
 * @Vich\Uploadable
 */
class Ally
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="User", mappedBy="ally", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $users;

    /**
     * @ORM\OneToMany(targetEntity="Proposal", mappedBy="ally", fetch="EXTRA_LAZY")
     */
    protected $proposals;

    /**
     * @ORM\Column(name="name",type="string", length=15, unique=true)
     * @Assert\NotBlank(message = "required")
     */
    protected $name;

    /**
     * @ORM\Column(name="sigle",type="string", length=5, unique=true)
     * @Assert\NotBlank(message = "required")
     */
    protected $sigle;

    /**
     * @ORM\Column(name="slogan",type="string", length=30, unique=true)
     * @Assert\NotBlank(message = "required")
     */
    protected $slogan;

    /**
     * @ORM\OneToMany(targetEntity="Grade", mappedBy="ally", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $grades;

    /**
     * @ORM\Column(name="bitcoin",type="bigint")
     */
    protected $bitcoin;

    /**
     * @ORM\Column(name="taxe",type="integer")
     */
    protected $taxe;

    /**
     * @ORM\Column(name="pna",type="array")
     */
    protected $pna;

    /**
     * @ORM\Column(name="allied",type="array")
     */
    protected $allied;

    /**
     * @ORM\Column(name="war",type="array")
     */
    protected $war;

    /**
     * @Assert\File(
     *     maxSize="800k",
     *     mimeTypes={"image/png", "image/jpeg", "image/bmp"}
     * )
     * @Vich\UploadableField(mapping="ally_img", fileNameProperty="imageName", size="imageSize" )
     *
     * @var File
     */
    private $imageFile= null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * Ally constructor.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proposals = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add proposal
     *
     * @param \App\Entity\Proposal $proposal
     *
     * @return Ally
     */
    public function addProposal(\App\Entity\Proposal $proposal)
    {
        $this->proposals[] = $proposal;

        return $this;
    }

    /**
     * Remove planet
     *
     * @param \App\Entity\Proposal $proposal
     */
    public function removeProposal(\App\Entity\Proposal $proposal)
    {
        $this->proposals->removeElement($proposal);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        $criteria = Criteria::create()
            ->orderBy(array('grade' => 'ASC'));

        return $this->users->matching($criteria);
    }

    /**
     * Add user
     *
     * @param \App\Entity\User $user
     *
     * @return Ally
     */
    public function addUser(\App\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\User $user
     */
    public function removeUser(\App\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Add ally pna
     *
     * @param \App\Entity\Ally $ally
     *
     * @return Ally
     */
    public function addAllyPna(\App\Entity\Ally $ally)
    {
        $this->pna[] = $ally;

        return $this;
    }

    /**
     * Remove ally pna
     *
     * @param \App\Entity\Ally $ally
     */
    public function removeAllyPna(\App\Entity\Ally $ally)
    {
        $this->pna->removeElement($ally);
    }

    /**
     * Add ally allied
     *
     * @param \App\Entity\Ally $ally
     *
     * @return Ally
     */
    public function addAllyAllied(\App\Entity\Ally $ally)
    {
        $this->allied[] = $ally;

        return $this;
    }

    /**
     * Remove ally allied
     *
     * @param \App\Entity\Ally $ally
     */
    public function removeAllyAllied(\App\Entity\Ally $ally)
    {
        $this->allied->removeElement($ally);
    }

    /**
     * Add ally war
     *
     * @param \App\Entity\Ally $ally
     *
     * @return Ally
     */
    public function addAllyWar(\App\Entity\Ally $ally)
    {
        $this->war[] = $ally;

        return $this;
    }

    /**
     * Remove ally war
     *
     * @param \App\Entity\Ally $ally
     */
    public function removeAllyWar(\App\Entity\Ally $ally)
    {
        $this->war->removeElement($ally);
    }

    /**
     * Add grade
     *
     * @param \App\Entity\Grade $grade
     *
     * @return Ally
     */
    public function addGrade(\App\Entity\Grade $grade)
    {
        $this->grade[] = $grade;

        return $this;
    }

    /**
     * Remove grade
     *
     * @param \App\Entity\Grade $grade
     */
    public function removeGrade(\App\Entity\Grade $grade)
    {
        $this->grade->removeElement($grade);
    }

    /**
     * @return mixed
     */
    public function getBitcoin()
    {
        return $this->bitcoin;
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

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageSize(?int $imageSize): void
    {
        $this->imageSize = $imageSize;
    }

    public function getImageSize(): ?int
    {
        return $this->imageSize;
    }

    /**
     * @return mixed
     */
    public function getTaxe()
    {
        return $this->taxe;
    }

    /**
     * @param mixed $taxe
     */
    public function setTaxe($taxe): void
    {
        $this->taxe = $taxe;
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
     * @param mixed $bitcoin
     */
    public function setBitcoin($bitcoin): void
    {
        $this->bitcoin = $bitcoin;
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
    public function getGrade()
    {
        return $this->grade;
    }

    /**
     * @param mixed $grade
     */
    public function setGrade($grade): void
    {
        $this->grade = $grade;
    }

    /**
     * @return mixed
     */
    public function getPna()
    {
        return $this->pna;
    }

    /**
     * @param mixed $pna
     */
    public function setPna($pna): void
    {
        $this->pna = $pna;
    }

    /**
     * @return mixed
     */
    public function getAllied()
    {
        return $this->allied;
    }

    /**
     * @param mixed $allied
     */
    public function setAllied($allied): void
    {
        $this->allied = $allied;
    }

    /**
     * @return mixed
     */
    public function getWar()
    {
        return $this->war;
    }

    /**
     * @param mixed $war
     */
    public function setWar($war): void
    {
        $this->war = $war;
    }

    /**
     * @return mixed
     */
    public function getSigle()
    {
        return $this->sigle;
    }

    /**
     * @param mixed $sigle
     */
    public function setSigle($sigle): void
    {
        $this->sigle = $sigle;
    }

    /**
     * @return mixed
     */
    public function getSlogan()
    {
        return $this->slogan;
    }

    /**
     * @param mixed $slogan
     */
    public function setSlogan($slogan): void
    {
        $this->slogan = $slogan;
    }

    /**
     * @return mixed
     */
    public function getGrades()
    {
        return $this->grades;
    }

    /**
     * @param mixed $grades
     */
    public function setGrades($grades): void
    {
        $this->grades = $grades;
    }

    /**
     * @return mixed
     */
    public function getProposals()
    {
        return $this->proposals;
    }

    /**
     * @param mixed $proposals
     */
    public function setProposals($proposals): void
    {
        $this->proposals = $proposals;
    }

    public function getId()
    {
        return $this->id;
    }
}
