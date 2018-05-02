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
     * @ORM\OneToMany(targetEntity="Pna", mappedBy="ally", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $pnas;

    /**
     * @ORM\OneToMany(targetEntity="Allied", mappedBy="ally", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $allieds;

    /**
     * @ORM\OneToMany(targetEntity="War", mappedBy="ally", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $wars;

    /**
     * @Assert\File(
     *     maxSize="1000k",
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
        $this->pnas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->allieds = new \Doctrine\Common\Collections\ArrayCollection();
        $this->wars = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Remove proposal
     *
     * @param \App\Entity\Proposal $proposal
     */
    public function removeProposal(\App\Entity\Proposal $proposal)
    {
        $this->proposals->removeElement($proposal);
    }

    /**
     * @return mixed
     */
    public function getPacts()
    {
        $pnas = count($this->getPnas());
        $allieds = count($this->getAllieds());
        $wars = count($this->getWars());

        $pacts = $pnas + $allieds + $wars;
        return $pacts;
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
     * @param \App\Entity\Pna $pna
     *
     * @return Ally
     */
    public function addAllyPna(\App\Entity\Pna $pna)
    {
        $this->pnas[] = $pna;

        return $this;
    }

    /**
     * Remove ally pna
     *
     * @param \App\Entity\Pna $pna
     */
    public function removeAllyPna(\App\Entity\Pna $pna)
    {
        $this->pnas->removeElement($pna);
    }

    /**
     * Add ally allied
     *
     * @param \App\Entity\Allied $allied
     *
     * @return Ally
     */
    public function addAllyAllied(\App\Entity\Allied $allied)
    {
        $this->allieds[] = $allied;

        return $this;
    }

    /**
     * Remove ally allied
     *
     * @param \App\Entity\Allied $allied
     */
    public function removeAllyAllied(\App\Entity\Allied $allied)
    {
        $this->allieds->removeElement($allied);
    }

    /**
     * Add ally war
     *
     * @param \App\Entity\War $war
     *
     * @return Ally
     */
    public function addAllyWar(\App\Entity\War $war)
    {
        $this->wars[] = $war;

        return $this;
    }

    /**
     * Remove ally war
     *
     * @param \App\Entity\War $war
     */
    public function removeAllyWar(\App\Entity\War $war)
    {
        $this->wars->removeElement($war);
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
    public function getRadarAlliance($sector)
    {
        $return = null;

        foreach($this->getUsers() as $user) {
            foreach ($user->getPlanets() as $planet) {
                if ($planet->getSector()->getPosition() == $sector) {
                    $radar = $planet->getRadar() + $planet->getSkyRadar();
                    if ($radar > $return || $return == null) {
                        $return = $radar;
                    }
                }
            }
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getSigleAllied($sigle)
    {
        foreach($this->getPnas() as $pna) {
            if ($pna->getAllyTag() == $sigle) {
                return $sigle;
            }
        }
        foreach($this->getAllieds() as $pact) {
            if ($pact->getAllyTag() == $sigle) {
                return $sigle;
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getSigleAlliedArray($sigles)
    {
        if($sigles) {
            foreach ($sigles as $sigle) {
                foreach ($this->getPnas() as $pna) {
                    if ($pna->getAllyTag() == $sigle) {
                        return null;
                    }
                }
                foreach ($this->getAllieds() as $pact) {
                    if ($pact->getAllyTag() == $sigle) {
                        return null;
                    }
                }
            }
        }
        return 'toto';
    }

    /**
     * @return int
     */
    public function getPlanets(): int
    {
        $return = 0;

        foreach($this->getUsers() as $user) {
            foreach ($user->getPlanets() as $planet) {
                $return++;
            }
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getFleets(): int
    {
        $return = 0;

        foreach($this->getUsers() as $user) {
            foreach ($user->getFleets() as $planet) {
                $return++;
            }
        }
        return $return;
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
    public function getPnas()
    {
        return $this->pnas;
    }

    /**
     * @param mixed $pnas
     */
    public function setPnas($pnas): void
    {
        $this->pnas = $pnas;
    }

    /**
     * @return mixed
     */
    public function getAllieds()
    {
        return $this->allieds;
    }

    /**
     * @param mixed $allieds
     */
    public function setAllieds($allieds): void
    {
        $this->allieds = $allieds;
    }

    /**
     * @return mixed
     */
    public function getWars()
    {
        return $this->wars;
    }

    /**
     * @param mixed $wars
     */
    public function setWars($wars): void
    {
        $this->wars = $wars;
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
