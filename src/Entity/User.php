<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="app_users")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable
 */
class User implements UserInterface, \Serializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="username",type="string", length=20, unique=true)
     * @Assert\NotBlank(message = "required")
     */
    protected $username;

    /**
     * @ORM\Column(name="orderPlanet",type="string", length=10)
     * @Assert\NotBlank(message = "required")
     */
    protected $orderPlanet = 'pos';

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Assert\Email(message = "email.mail_format")
     * @Assert\NotBlank(message = "required")
     */
    protected $email;

    /**
     * @Assert\Ip
     */
    protected $ipAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Ally", inversedBy="users", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ally_id", referencedColumnName="id")
     */
    protected $ally;

    /**
     * @ORM\Column(name="allyBan",type="datetime", nullable=true)
     */
    protected $allyBan = null;

    /**
     * @ORM\OneToMany(targetEntity="Proposal", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $proposals;

    /**
     * @ORM\Column(name="joinAllyAt",type="datetime", nullable=true)
     */
    protected $joinAllyAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="users", fetch="EXTRA_LAZY")
     */
    protected $grade;

    /**
     * @ORM\OneToOne(targetEntity="Rank", inversedBy="user", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="rank_id", referencedColumnName="id")
     */
    protected $rank;

    /**
     * @ORM\OneToOne(targetEntity="Commander", inversedBy="user", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="commander_id", referencedColumnName="id")
     */
    protected $commander;

    /**
     *
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank(message = "required")
     */
    protected $password;

    /**
     * @ORM\OneToMany(targetEntity="Planet", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $planets;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $messages;

    /**
     * @ORM\ManyToMany(targetEntity="Salon", mappedBy="users", fetch="EXTRA_LAZY")
     */
    protected $salons;

    /**
     * @ORM\Column(name="salonAt",type="datetime", nullable=true)
     */
    protected $salonAt = null;

    /**
     * @ORM\Column(name="salonBan",type="datetime", nullable=true)
     */
    protected $salonBan = null;

    /**
     * @ORM\OneToMany(targetEntity="S_Content", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $sContents;

    /**
     * @ORM\OneToMany(targetEntity="Report", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $reports;

    /**
     * @ORM\Column(name="viewMessage",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $viewMessage = true;

    /**
     * @ORM\Column(name="viewReport",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $viewReport = true;

    /**
     * @ORM\Column(name="tutorial",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $tutorial = true;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="fleet_id", referencedColumnName="id")
     */
    protected $fleets;

    /**
     * @ORM\Column(name="bitcoin",type="decimal", precision=28, scale=5)
     */
    protected $bitcoin;

    /**
     * @ORM\Column(name="scientistProduction",type="decimal", precision=28, scale=5)
     */
    protected $scientistProduction;

    /**
     * @ORM\Column(name="onde",type="integer", nullable=true)
     */
    protected $onde = 0;

    /**
     * @ORM\Column(name="industry",type="integer", nullable=true)
     */
    protected $industry = 0;

    /**
     * @ORM\Column(name="lightShip",type="integer", nullable=true)
     */
    protected $lightShip = 0;

    /**
     * @ORM\Column(name="heavyShip",type="integer", nullable=true)
     */
    protected $heavyShip = 0;

    /**
     * @ORM\Column(name="discipline",type="integer", nullable=true)
     */
    protected $discipline = 0;

    /**
     * @ORM\Column(name="hyperespace",type="integer", nullable=true)
     */
    protected $hyperespace = 0;

    /**
     * @ORM\Column(name="barge",type="integer", nullable=true)
     */
    protected $barge = 0;

    /**
     * @ORM\Column(name="utility",type="integer", nullable=true)
     */
    protected $utility = 0;

    /**
     * @ORM\Column(name="demography",type="integer", nullable=true)
     */
    protected $demography = 0;

    /**
     * @ORM\Column(name="terraformation",type="integer", nullable=true)
     */
    protected $terraformation = 0;

    /**
     * @ORM\Column(name="cargo",type="integer", nullable=true)
     */
    protected $cargo = 0;

    /**
     * @ORM\Column(name="recycleur",type="integer", nullable=true)
     */
    protected $recycleur = 0;

    /**
     * @ORM\Column(name="armement",type="integer", nullable=true)
     */
    protected $armement = 0;

    /**
     * @ORM\Column(name="missile",type="integer", nullable=true)
     */
    protected $missile = 0;

    /**
     * @ORM\Column(name="laser",type="integer", nullable=true)
     */
    protected $laser = 0;

    /**
     * @ORM\Column(name="plasma",type="integer", nullable=true)
     */
    protected $plasma = 0;

    /**
     * @ORM\Column(name="searchAt",type="datetime", nullable=true)
     */
    protected $searchAt = null;

    /**
     * @ORM\Column(name="search",type="string", nullable=true)
     */
    protected $search = null;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     */
    protected $createdAt = null;

    /**
     * @ORM\Column(name="lastActivity",type="datetime", nullable=true)
     */
    protected $lastActivity = null;

    /**
     * @ORM\Column(name="gameOver",type="string", nullable=true)
     */
    protected $gameOver = null;

    /**
     * @Assert\File(
     *     maxSize="1000k",
     *     mimeTypes={"image/png", "image/jpeg", "image/bmp"}
     * )
     * @Vich\UploadableField(mapping="user_img", fileNameProperty="imageName", size="imageSize" )
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
     * User constructor.
     */
    public function __construct()
    {
        $this->planets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->proposals = new \Doctrine\Common\Collections\ArrayCollection();
        $this->salons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->reports = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fleets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->scientistProduction = 1;
        $this->bitcoin = 25000;
    }

    /**
     * @return mixed
     */
    public function getPlanets()
    {
        if ($this->getOrderPlanet() == 'alpha') {
            $criteria = Criteria::create()
                ->orderBy(array('name' => 'ASC'));
        } elseif ($this->getOrderPlanet() == 'colo') {
            $criteria = Criteria::create()
                ->orderBy(array('nbColo' => 'ASC'));
        } else {
        $criteria = Criteria::create()
            ->orderBy(['id' => 'ASC']);
        }

        return $this->planets->matching($criteria);
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param mixed $messages
     */
    public function setMessages($messages): void
    {
        $this->messages = $messages;
    }

    /**
     * Add salon
     *
     * @param \App\Entity\Salon $salon
     *
     * @return User
     */
    public function addSalon(\App\Entity\Salon $salon)
    {
        $this->salons[] = $salon;

        return $this;
    }

    /**
     * Remove salon
     *
     * @param \App\Entity\Salon $salon
     */
    public function removeSalon(\App\Entity\Salon $salon)
    {
        $this->salons->removeElement($salon);
    }

    /**
     * Add content
     *
     * @param \App\Entity\S_Content $sContent
     *
     * @return User
     */
    public function addSContent(\App\Entity\S_Content $sContent)
    {
        $this->sContents[] = $sContent;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \App\Entity\S_Content $sContent
     */
    public function removeSContent(\App\Entity\S_Content $sContent)
    {
        $this->sContents->removeElement($sContent);
    }

    /**
     * Add message
     *
     * @param \App\Entity\Message $message
     *
     * @return User
     */
    public function addMessage(\App\Entity\Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \App\Entity\Message $message
     */
    public function removeMessage(\App\Entity\Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Add report
     *
     * @param \App\Entity\Report $report
     *
     * @return User
     */
    public function addReport(\App\Entity\Report $report)
    {
        $this->reports[] = $report;

        return $this;
    }

    /**
     * Remove report
     *
     * @param \App\Entity\Report $report
     */
    public function removeReport(\App\Entity\Report $report)
    {
        $this->reports->removeElement($report);
    }

    /**
     * Add planet
     *
     * @param \App\Entity\Planet $planet
     *
     * @return User
     */
    public function addPlanet(\App\Entity\Planet $planet)
    {
        $this->planets[] = $planet;

        return $this;
    }

    /**
     * Remove planet
     *
     * @param \App\Entity\Planet $planet
     */
    public function removePlanet(\App\Entity\Planet $planet)
    {
        $this->planets->removeElement($planet);
    }

    /**
     * Add proposal
     *
     * @param \App\Entity\Proposal $proposal
     *
     * @return User
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
     * @return mixed
     */
    public function getAllyEnnemy()
    {
        if($this->ally) {
            return $this->ally->getWars();
        } else {
            return [];
        }
    }

    /**
     * @return mixed
     */
    public function getSigleAlliedArray($sigles)
    {
        $ally = $this->getAlly();
        if($sigles && $ally) {
            foreach ($sigles as $sigle) {
                foreach ($ally->getPnas() as $pna) {
                    if ($pna->getAllyTag() == $sigle && $pna->getAccepted() == true) {
                        return null;
                    }
                }
                foreach ($ally->getAllieds() as $pact) {
                    if ($pact->getAllyTag() == $sigle && $pact->getAccepted() == true) {
                        return null;
                    }
                }
                foreach ($ally->getPeaces() as $peace) {
                    if ($peace->getAllyTag() == $sigle && $peace->getAccepted() == true) {
                        return null;
                    }
                }
            }
        }
        return 'toto';
    }

    /**
     * @return mixed
     */
    public function getAllyFriends()
    {
        if($this->getAlly()) {
            return $this->getAlly()->getAllieds();
        } else {
            return [];
        }
    }

    /**
     * @return null|string
     */
    public function getRessourceFull()
    {
        foreach($this->planets as $planet) {
            if($planet->getNiobium() >= $planet->getNiobiumMax() || $planet->getWater() >= $planet->getWaterMax()) {
                return ' planetFull';
            }
        }
        return null;
    }

    /**
     * @return null|\App\Entity\Peace
     */
    public function getPeaces()
    {
        if($this->getAlly()) {
            if($this->getAlly()->getPeaces()) {
                foreach($this->getAlly()->getPeaces() as $peace) {
                    if($peace->getAccepted() == 1 && $peace->getType() == false) {
                        return $peace;
                    }
                }
            }
        } else {
            return null;
        }
        return null;
    }

    /**
     * @return int
     */
    public function getAllShips(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + $planet->getShipOn();
        }
        foreach($this->fleets as $fleet) {
            $return = $return + $fleet->getNbrShips();
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllShipsCost(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            if($planet->getProduct()) {
                $return = $return + ($planet->getProduct()->getNbrSignatures() / 3);
            }
            $return = $return + ($planet->getNbrSignatures() / 5);
        }
        foreach($this->fleets as $fleet) {
            $return = $return + $fleet->getNbrSignatures();
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getBuildingCost(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + $planet->getBuildingCost();
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllShipsPoint(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getNbrSignatures() / 5);
        }
        foreach($this->fleets as $fleet) {
            $return = $return + ($fleet->getNbrSignatures() / 5);
        }
        return $return;
    }

    /**
     * @param $time
     * @return int
     */
    public function getTimeConstruct($time): int
    {
        return round($time / $this->getScientistProduction());
    }

    /**
     * @return int
     */
    public function getAllSoldier(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + $planet->getSoldier();
        }
        foreach($this->fleets as $fleet) {
            $return = $return + $fleet->getSoldier();
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getAllPlanets()
    {
        $nbr = 0;
        foreach($this->planets as $planet) {
            if($planet->getEmpty() == false) {
                $nbr = $nbr + 1;
            }
        }
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getColPlanets()
    {
        $nbr = 0;
        foreach($this->planets as $planet) {
            if($planet->getMoon() == false && $planet->getEmpty() == false) {
                $nbr = $nbr + 1;
            }
        }
        return $nbr;
    }

    /**
     * @return mixed
     */
    public function getMotherShip()
    {
        foreach($this->planets as $planet) {
            if($planet->getMotherShip()) {
                return 'gotOne';
            }
            if($planet->getProduct()) {
                if ($planet->getProduct()->getMotherShip()) {
                    return 'gotOne';
                }
            }
        }
        foreach($this->fleets as $fleet) {
            if($fleet->getMotherShip()) {
                return 'gotOne';
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getColonizer()
    {
        $nbr = 0;
        foreach($this->planets as $planet) {
            if($planet->getColonizer()) {
                $nbr = $nbr + $planet->getColonizer();
            }
            if($planet->getProduct()) {
                if ($planet->getProduct()->getColonizer()) {
                    $nbr = $nbr + $planet->getColonizer();
                }
            }
        }
        foreach($this->fleets as $fleet) {
            if($fleet->getColonizer()) {
                $nbr = $nbr + $planet->getColonizer();
            }
        }
        if($nbr > 2) {
            return $nbr;
        }
        return null;
    }

    /**
     * @return int
     */
    public function getAllWorker(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getWorker());
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllScientist(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getScientist());
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllNiobium(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getNbProduction() * 60);
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllWater(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getWtProduction() * 60);
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getWhereRadar($sector, $gal)
    {
        $return = null;

        foreach($this->getPlanets() as $planet) {
            if ($planet->getSector()->getPosition() == $sector && $planet->getSector()->getGalaxy()->getPosition() == $gal) {
                $radar = $planet->getRadar() + $planet->getSkyRadar();
                if($radar > $return || $return == null) {
                    $return = $radar;
                }
            }
        }
        if ($this->getAlly()) {
            if ($this->getAlly()->getRadarAlliance($sector) > $return || $return == null) {
                $return = $this->getAlly()->getRadarAlliance($sector);
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
     * @param mixed $bitcoin
     */
    public function setBitcoin($bitcoin): void
    {
        $this->bitcoin = $bitcoin;
    }


    public function getId()
    {
        return $this->id;
    }

    public function getSalt()
    {
        // you *may* need a real salt depending on your encoder
        // see section on salt below
        return null;
    }

    public function getRoles()
    {
        if($this->username === 'Thea' || $this->username === 'EndeR') {
            return ['ROLE_PRIVATE'];
        } else {
            return ['ROLE_USER'];
        }
    }

    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->username,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
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
    public function getAlly()
    {
        return $this->ally;
    }

    /**
     * @param mixed $ally
     */
    public function setAlly($ally): void
    {
        $this->ally = $ally;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
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
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param mixed $rank
     */
    public function setRank($rank): void
    {
        $this->rank = $rank;
    }

    /**
     * @return mixed
     */
    public function getJoinAllyAt()
    {
        return $this->joinAllyAt;
    }

    /**
     * @param mixed $joinAllyAt
     */
    public function setJoinAllyAt($joinAllyAt): void
    {
        $this->joinAllyAt = $joinAllyAt;
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

    /**
     * Add fleet
     *
     * @param \App\Entity\Fleet $fleet
     *
     * @return User
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

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return mixed
     */
    public function getScientistProduction()
    {
        return $this->scientistProduction;
    }

    /**
     * @param mixed $scientistProduction
     */
    public function setScientistProduction($scientistProduction): void
    {
        $this->scientistProduction = $scientistProduction;
    }

    /**
     * @return mixed
     */
    public function getOnde()
    {
        return $this->onde;
    }

    /**
     * @param mixed $onde
     */
    public function setOnde($onde): void
    {
        $this->onde = $onde;
    }

    /**
     * @return mixed
     */
    public function getIndustry()
    {
        return $this->industry;
    }

    /**
     * @param mixed $industry
     */
    public function setIndustry($industry): void
    {
        $this->industry = $industry;
    }

    /**
     * @return mixed
     */
    public function getLightShip()
    {
        return $this->lightShip;
    }

    /**
     * @param mixed $lightShip
     */
    public function setLightShip($lightShip): void
    {
        $this->lightShip = $lightShip;
    }

    /**
     * @return mixed
     */
    public function getHeavyShip()
    {
        return $this->heavyShip;
    }

    /**
     * @param mixed $heavyShip
     */
    public function setHeavyShip($heavyShip): void
    {
        $this->heavyShip = $heavyShip;
    }

    /**
     * @return mixed
     */
    public function getDiscipline()
    {
        return $this->discipline;
    }

    /**
     * @param mixed $discipline
     */
    public function setDiscipline($discipline): void
    {
        $this->discipline = $discipline;
    }

    /**
     * @return mixed
     */
    public function getHyperespace()
    {
        return $this->hyperespace;
    }

    /**
     * @param mixed $hyperespace
     */
    public function setHyperespace($hyperespace): void
    {
        $this->hyperespace = $hyperespace;
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
    public function getUtility()
    {
        return $this->utility;
    }

    /**
     * @param mixed $utility
     */
    public function setUtility($utility): void
    {
        $this->utility = $utility;
    }

    /**
     * @return mixed
     */
    public function getDemography()
    {
        return $this->demography;
    }

    /**
     * @param mixed $demography
     */
    public function setDemography($demography): void
    {
        $this->demography = $demography;
    }

    /**
     * @return mixed
     */
    public function getTerraformation()
    {
        return $this->terraformation;
    }

    /**
     * @param mixed $terraformation
     */
    public function setTerraformation($terraformation): void
    {
        $this->terraformation = $terraformation;
    }

    /**
     * @return mixed
     */
    public function getCargo()
    {
        return $this->cargo;
    }

    /**
     * @param mixed $cargo
     */
    public function setCargo($cargo): void
    {
        $this->cargo = $cargo;
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
    public function getArmement()
    {
        return $this->armement;
    }

    /**
     * @param mixed $armement
     */
    public function setArmement($armement): void
    {
        $this->armement = $armement;
    }

    /**
     * @return mixed
     */
    public function getMissile()
    {
        return $this->missile;
    }

    /**
     * @param mixed $missile
     */
    public function setMissile($missile): void
    {
        $this->missile = $missile;
    }

    /**
     * @return mixed
     */
    public function getLaser()
    {
        return $this->laser;
    }

    /**
     * @param mixed $laser
     */
    public function setLaser($laser): void
    {
        $this->laser = $laser;
    }

    /**
     * @return mixed
     */
    public function getPlasma()
    {
        return $this->plasma;
    }

    /**
     * @param mixed $plasma
     */
    public function setPlasma($plasma): void
    {
        $this->plasma = $plasma;
    }

    /**
     * @return mixed
     */
    public function getSearchAt()
    {
        return $this->searchAt;
    }

    /**
     * @param mixed $searchAt
     */
    public function setSearchAt($searchAt): void
    {
        $this->searchAt = $searchAt;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }

    /**
     * @return mixed
     */
    public function getGameOver()
    {
        return $this->gameOver;
    }

    /**
     * @param mixed $gameOver
     */
    public function setGameOver($gameOver): void
    {
        $this->gameOver = $gameOver;
    }

    /**
     * @return mixed
     */
    public function getViewMessage()
    {
        return $this->viewMessage;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * @param mixed $reports
     */
    public function setReports($reports): void
    {
        $this->reports = $reports;
    }

    /**
     * @return mixed
     */
    public function getViewReport()
    {
        return $this->viewReport;
    }

    /**
     * @param mixed $viewReport
     */
    public function setViewReport($viewReport): void
    {
        $this->viewReport = $viewReport;
    }

    /**
     * @param mixed $viewMessage
     */
    public function setViewMessage($viewMessage): void
    {
        $this->viewMessage = $viewMessage;
    }

    /**
     * @return mixed
     */
    public function getSalons()
    {
        return $this->salons;
    }

    /**
     * @return mixed
     */
    public function getSalonBan()
    {
        return $this->salonBan;
    }

    /**
     * @param mixed $salonBan
     */
    public function setSalonBan($salonBan): void
    {
        $this->salonBan = $salonBan;
    }

    /**
     * @return mixed
     */
    public function getSContents()
    {
        return $this->sContents;
    }

    /**
     * @param mixed $sContents
     */
    public function setSContents($sContents): void
    {
        $this->sContents = $sContents;
    }

    /**
     * @param mixed $salons
     */
    public function setSalons($salons): void
    {
        $this->salons = $salons;
    }

    /**
     * @return mixed
     */
    public function getSalonAt()
    {
        return $this->salonAt;
    }

    /**
     * @param mixed $salonAt
     */
    public function setSalonAt($salonAt): void
    {
        $this->salonAt = $salonAt;
    }

    /**
     * @return mixed
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param mixed $ipAddress
     */
    public function setIpAddress($ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return mixed
     */
    public function getLastActivity()
    {
        return $this->lastActivity;
    }

    /**
     * @param mixed $lastActivity
     */
    public function setLastActivity($lastActivity): void
    {
        $this->lastActivity = $lastActivity;
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
    public function getAllyBan()
    {
        return $this->allyBan;
    }

    /**
     * @return mixed
     */
    public function getOrderPlanet()
    {
        return $this->orderPlanet;
    }

    /**
     * @param mixed $orderPlanet
     */
    public function setOrderPlanet($orderPlanet): void
    {
        $this->orderPlanet = $orderPlanet;
    }

    /**
     * @return mixed
     */
    public function getTutorial()
    {
        return $this->tutorial;
    }

    /**
     * @param mixed $tutorial
     */
    public function setTutorial($tutorial): void
    {
        $this->tutorial = $tutorial;
    }

    /**
     * @param mixed $allyBan
     */
    public function setAllyBan($allyBan): void
    {
        $this->allyBan = $allyBan;
    }
}
