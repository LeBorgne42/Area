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
     * @Assert\Regex(
     *     pattern="/[a-zA-Z0-9]/")
     */
    protected $username;

    /**
     * @ORM\Column(name="orderPlanet",type="string", length=10)
     * @Assert\NotBlank(message = "required")
     */
    protected $orderPlanet;

    /**
     * @ORM\Column(type="string", length=40, unique=true)
     * @Assert\Email(message = "email.mail_format")
     * @Assert\NotBlank(message = "required")
     */
    protected $email;

    /**
     * @ORM\Column(name="experience",type="integer")
     */
    protected $experience;

    /**
     * @ORM\Column(type="string", length=40, unique=true, nullable=true)
     * @Assert\Ip
     * @Assert\NotBlank(message = "required")
     */
    protected $ipAddress;

    /**
     * @ORM\Column(name="cheat",type="smallint")
     */
    protected $cheat;

    /**
     * @ORM\ManyToOne(targetEntity="Ally", inversedBy="users", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ally_id", referencedColumnName="id")
     */
    protected $ally;

    /**
     * @ORM\Column(name="allyBan",type="datetime", nullable=true)
     */
    protected $allyBan;

    /**
     * @ORM\Column(name="dailyConnect",type="datetime", nullable=true)
     */
    protected $dailyConnect;

    /**
     * @ORM\Column(name="zombie_att",type="smallint")
     */
    protected $zombieAtt;

    /**
     * @ORM\Column(name="zombie_at",type="datetime", nullable=true)
     */
    protected $zombieAt;

    /**
     * @ORM\OneToMany(targetEntity="Proposal", mappedBy="user", fetch="EXTRA_LAZY")
     */
    protected $proposals;

    /**
     * @ORM\Column(name="joinAllyAt",type="datetime", nullable=true)
     */
    protected $joinAllyAt;

    /**
     * @ORM\ManyToOne(targetEntity="Grade", inversedBy="users", fetch="EXTRA_LAZY")
     */
    protected $grade;

    /**
     * @ORM\ManyToMany(targetEntity="Quest", inversedBy="users")
     */
    protected $quests;

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
    protected $salonAt;

    /**
     * @ORM\Column(name="salonBan",type="datetime", nullable=true)
     */
    protected $salonBan;

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
    protected $viewMessage;

    /**
     * @ORM\Column(name="zombie",type="boolean")
     */
    protected $zombie;

    /**
     * @ORM\Column(name="merchant",type="boolean")
     */
    protected $merchant;

    /**
     * @ORM\Column(name="viewReport",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $viewReport;


    /**
     * @ORM\Column(name="tutorial",type="smallint")
     */
    protected $tutorial;


    /**
     * @ORM\Column(name="vote_ally",type="smallint")
     */
    protected $voteAlly;

    /**
     * @ORM\Column(name="vote_name",type="string", nullable=true)
     */
    protected $voteName;

    /**
     * @ORM\Column(name="newletter",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $newletter;

    /**
     * @ORM\Column(name="confirmed",type="boolean")
     */
    protected $confirmed;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="fleet_id", referencedColumnName="id")
     */
    protected $fleets;

    /**
     * @ORM\OneToMany(targetEntity="Fleet_List", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="fleet_list_id", referencedColumnName="id")
     */
    protected $fleetLists;

    /**
     * @ORM\Column(name="bitcoin",type="bigint")
     */
    protected $bitcoin;

    /**
     * @ORM\Column(name="scientistProduction",type="decimal", precision=28, scale=5)
     */
    protected $scientistProduction;

    /**
     * @ORM\Column(name="onde",type="smallint")
     */
    protected $onde;

    /**
     * @ORM\Column(name="industry",type="integer")
     */
    protected $industry;

    /**
     * @ORM\Column(name="lightShip",type="smallint")
     */
    protected $lightShip;

    /**
     * @ORM\Column(name="heavyShip",type="smallint")
     */
    protected $heavyShip;

    /**
     * @ORM\Column(name="discipline",type="smallint")
     */
    protected $discipline;

    /**
     * @ORM\Column(name="hyperespace",type="smallint")
     */
    protected $hyperespace;

    /**
     * @ORM\Column(name="barge",type="smallint")
     */
    protected $barge;

    /**
     * @ORM\Column(name="utility",type="smallint")
     */
    protected $utility;

    /**
     * @ORM\Column(name="demography",type="smallint")
     */
    protected $demography;

    /**
     * @ORM\Column(name="barbed",type="smallint")
     */
    protected $barbed;

    /**
     * @ORM\Column(name="tank",type="smallint")
     */
    protected $tank;

    /**
     * @ORM\Column(name="expansion",type="smallint")
     */
    protected $expansion;
    /**
     * @ORM\Column(name="terraformation",type="smallint")
     */
    protected $terraformation;

    /**
     * @ORM\Column(name="cargo",type="smallint")
     */
    protected $cargo;

    /**
     * @ORM\Column(name="recycleur",type="smallint")
     */
    protected $recycleur;

    /**
     * @ORM\Column(name="armement",type="smallint")
     */
    protected $armement;

    /**
     * @ORM\Column(name="missile",type="smallint")
     */
    protected $missile;

    /**
     * @ORM\Column(name="laser",type="smallint")
     */
    protected $laser;

    /**
     * @ORM\Column(name="plasma",type="smallint")
     */
    protected $plasma;

    /**
     * @ORM\Column(name="politic_cargo",type="smallint")
     */
    protected $politicCargo;

    /**
     * @ORM\Column(name="politic_recycleur",type="smallint")
     */
    protected $politicRecycleur;

    /**
     * @ORM\Column(name="politic_worker",type="smallint")
     */
    protected $politicWorker;

    /**
     * @ORM\Column(name="politic_prod",type="smallint")
     */
    protected $politicProd;

    /**
     * @ORM\Column(name="politic_cost_soldier",type="smallint")
     */
    protected $politicCostSoldier;

    /**
     * @ORM\Column(name="politic_cost_scientist",type="smallint")
     */
    protected $politicCostScientist;

    /**
     * @ORM\Column(name="politic_cost_tank",type="smallint")
     */
    protected $politicCostTank;

    /**
     * @ORM\Column(name="politic_worker_def",type="smallint")
     */
    protected $politicWorkerDef;

    /**
     * @ORM\Column(name="politic_tank_def",type="smallint")
     */
    protected $politicTankDef;

    /**
     * @ORM\Column(name="politic_soldier_att",type="smallint")
     */
    protected $politicSoldierAtt;

    /**
     * @ORM\Column(name="politic_armement",type="smallint")
     */
    protected $politicArmement;

    /**
     * @ORM\Column(name="politic_armor",type="smallint")
     */
    protected $politicArmor;

    /**
     * @ORM\Column(name="politic_colonisation",type="smallint")
     */
    protected $politicColonisation;

    /**
     * @ORM\Column(name="politic_invade",type="smallint")
     */
    protected $politicInvade;

    /**
     * @ORM\Column(name="politic_merchant",type="smallint")
     */
    protected $politicMerchant;

    /**
     * @ORM\Column(name="politic_search",type="smallint")
     */
    protected $politicSearch;

    /**
     * @ORM\Column(name="politic_soldier_sale",type="smallint")
     */
    protected $politicSoldierSale;

    /**
     * @ORM\Column(name="politic_barge",type="smallint")
     */
    protected $politicBarge;

    /**
     * @ORM\Column(name="politic_pdg",type="smallint")
     */
    protected $politicPdg;

    /**
     * @ORM\Column(name="searchAt",type="datetime", nullable=true)
     */
    protected $searchAt;

    /**
     * @ORM\Column(name="search",type="string", nullable=true)
     */
    protected $search;

    /**
     * @ORM\Column(name="created_at",type="datetime")
     */
    protected $createdAt;

    /**
     * @ORM\Column(name="lastActivity",type="datetime", nullable=true)
     */
    protected $lastActivity;

    /**
     * @ORM\Column(name="gameOver",type="string", nullable=true)
     */
    protected $gameOver;

    /**
     * @Assert\File(
     *     maxSize="1000k",
     *     mimeTypes={"image/png", "image/jpeg", "image/bmp"}
     * )
     * @Vich\UploadableField(mapping="user_img", fileNameProperty="imageName", size="imageSize" )
     *
     * @var File
     */
    private $imageFile;

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
        $this->quests = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fleetLists = new \Doctrine\Common\Collections\ArrayCollection();
        $this->scientistProduction = 1;
        $this->bitcoin = 25000;
        $this->cheat = 0;
        $this->newletter = 1;
        $this->viewMessage = 1;
        $this->viewReport = 1;
        $this->tutorial = 1;
        $this->confirmed = 1;
        $this->salonAt = null;
        $this->salonBan = null;
        $this->joinAllyAt = null;
        $this->allyBan = null;
        $this->orderPlanet = 'pos';
        $this->searchAt = null;
        $this->search = null;
        $this->createdAt = null;
        $this->lastActivity = null;
        $this->gameOver = null;
        $this->imageFile = null;
        $this->onde = 0;
        $this->industry = 0;
        $this->lightShip = 0;
        $this->heavyShip = 0;
        $this->discipline = 0;
        $this->hyperespace = 0;
        $this->barge = 0;
        $this->demography = 0;
        $this->terraformation = 0;
        $this->utility = 0;
        $this->cargo = 0;
        $this->recycleur = 0;
        $this->armement = 0;
        $this->missile = 0;
        $this->laser = 0;
        $this->plasma = 0;
        $this->experience = 0;
        $this->barbed = 0;
        $this->tank = 0;
        $this->expansion = 0;
        $this->politicArmement = 0;
        $this->politicCostScientist = 0;
        $this->politicArmor = 0;
        $this->politicBarge = 0;
        $this->politicCargo = 0;
        $this->politicColonisation = 0;
        $this->politicCostSoldier = 0;
        $this->politicCostTank = 0;
        $this->politicInvade = 0;
        $this->politicMerchant = 0;
        $this->politicPdg = 0;
        $this->politicProd = 0;
        $this->politicRecycleur = 0;
        $this->politicSearch = 0;
        $this->politicSoldierAtt = 0;
        $this->politicSoldierSale = 0;
        $this->politicTankDef = 0;
        $this->politicWorker = 0;
        $this->politicWorkerDef = 0;
        $this->voteAlly = 0;
        $this->voteName = null;
        $this->dailyConnect = null;
        $this->zombieAt = null;
        $this->zombie = 0;
        $this->zombieAtt = 1;
        $this->merchant = 0;
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
    public function getWhichQuest()
    {
        $return = ['private_message', 'salon_message', 'pdg', 'spy_planet', 'sell', 'cohort', 'destroy_fleet', 'recycle', 'invade', 'soldier', 'scientist', 'ships'];
        if ($this->getColPlanets() < 20) {
            $return[] = 'colonize';
        }
        if ($this->checkResearch()) {
            $return[] = 'research';
        }
        if (!$this->getImageName()) {
            $return[] = 'logo';
        }
        return $return;
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
                        return $pna->getAllyTag();
                    }
                }
                foreach ($ally->getAllieds() as $pact) {
                    if ($pact->getAllyTag() == $sigle && $pact->getAccepted() == true) {
                        return $pact->getAllyTag();
                    }
                }
                foreach ($ally->getPeaces() as $peace) {
                    if ($peace->getAllyTag() == $sigle && $peace->getAccepted() == true) {
                        return $peace->getAllyTag();
                    }
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getSigleAllied($sigle)
    {
        $ally = $this->getAlly();
        if($sigle && $ally) {
            foreach ($ally->getPnas() as $pna) {
                if ($pna->getAllyTag() == $sigle && $pna->getAccepted() == true) {
                    return $pna->getAllyTag();
                }
            }
            foreach ($ally->getAllieds() as $pact) {
                if ($pact->getAllyTag() == $sigle && $pact->getAccepted() == true) {
                    return $pact->getAllyTag();
                }
            }
            foreach ($ally->getPeaces() as $peace) {
                if ($peace->getAllyTag() == $sigle && $peace->getAccepted() == true) {
                    return $peace->getAllyTag();
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getOurAllyPact($sigle)
    {
        $ally = $this->getAlly();
        if($sigle && $ally) {
            foreach ($ally->getPnas() as $pna) {
                if ($pna->getAllyTag() == $sigle && $pna->getAccepted() == true) {
                    return 'toto';
                }
            }
            foreach ($ally->getAllieds() as $pact) {
                if ($pact->getAllyTag() == $sigle && $pact->getAccepted() == true) {
                    return 'toto';
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getFirstPlanet()
    {
        foreach ($this->planets as $planet) {
            return $planet->getSector()->getGalaxy()->getPosition() . ":" . $planet->getSector()->getPosition() . ":" . $planet->getPosition();
        }
        return 'Game over';
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
     * @return mixed
     */
    public function checkQuests($name)
    {
        foreach($this->quests as $quest) {
            if($quest->getName() == $name) {
                return $quest;
            }
        }
        return NULL;
    }

    /**
     * @return mixed
     */
    public function getBarbedAdv()
    {
        $return = 1;
        if($this->barbed == 1) {
            $return = 1.05;
        } elseif ($this->barbed == 2) {
            $return = 1.10;
        } elseif ($this->barbed == 3) {
            $return = 1.15;
        } elseif ($this->barbed == 4) {
            $return = 1.20;
        } elseif ($this->barbed == 5) {
            $return = 1.25;
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function checkResearch()
    {
        $return = 'continue';
        if($this->utility < 3) {
            return $return;
        } elseif ($this->terraformation < 18) {
            return $return;
        } elseif ($this->cargo < 5) {
            return $return;
        } elseif ($this->barge < 1) {
            return $return;
        } elseif ($this->recycleur < 1) {
            return $return;
        } elseif ($this->barbed < 5) {
            return $return;
        } elseif ($this->tank < 1) {
            return $return;
        } elseif ($this->expansion < 2) {
            return $return;
        } elseif ($this->onde < 5) {
            return $return;
        } elseif ($this->hyperespace < 1) {
            return $return;
        } elseif ($this->demography < 5) {
            return $return;
        } elseif ($this->discipline < 3) {
            return $return;
        } elseif ($this->armement < 5) {
            return $return;
        } elseif ($this->missile < 3) {
            return $return;
        } elseif ($this->laser < 3) {
            return $return;
        } elseif ($this->plasma < 3) {
            return $return;
        } elseif ($this->industry < 5) {
            return $return;
        } elseif ($this->lightShip < 3) {
            return $return;
        } elseif ($this->heavyShip < 3) {
            return $return;
        } elseif ($this->ally) {
            if ($this->politicCostScientist < 5){
                return $return;
            } elseif ($this->politicArmor < 5){
                return $return;
            } elseif ($this->politicBarge < 5){
                return $return;
            } elseif ($this->politicCargo < 5){
                return $return;
            } elseif ($this->politicColonisation < 5){
                return $return;
            } elseif ($this->politicCostSoldier < 5){
                return $return;
            } elseif ($this->politicCostTank < 5){
                return $return;
            } elseif ($this->politicInvade < 5){
                return $return;
            } elseif ($this->politicMerchant < 5){
                return $return;
            } elseif ($this->politicPdg < 5){
                return $return;
            } elseif ($this->politicProd < 5){
                return $return;
            } elseif ($this->politicRecycleur < 5){
                return $return;
            } elseif ($this->politicSearch < 5){
                return $return;
            } elseif ($this->politicSoldierAtt < 5){
                return $return;
            } elseif ($this->politicTankDef < 5){
                return $return;
            } elseif ($this->politicWorker < 5){
                return $return;
            } elseif ($this->politicWorkerDef < 5){
                return $return;
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getWhichResearch($search)
    {
        if ($this->ally) {
            if ('prod_ally' == $search) {
                return $this->politicProd;
            } elseif ('recycleur_ally' == $search) {
                return $this->politicRecycleur;
            } elseif ('worker_ally' == $search) {
                return $this->politicWorker;
            }
            if ($this->ally->getLevel() >= 1) {
                if ('armement_ally' == $search && $this->ally->getPolitic() == 'fascism') {
                    return $this->politicArmement;
                } elseif ('worker_def_ally' == $search) {
                    return $this->politicWorkerDef;
                } elseif ('search_ally' == $search) {
                    return $this->politicSearch;
                }
            }
            if ($this->ally->getLevel() >= 2) {
                if ('cost_scientist_ally' == $search) {
                    return $this->politicCostScientist;
                } elseif ('armement_ally' == $search && $this->ally->getPolitic() == 'communism') {
                    return $this->politicArmement;
                } elseif ('barge_ally' == $search) {
                    return $this->politicBarge;
                }
            }
            if ($this->ally->getLevel() >= 3) {
                if ('soldier_att_ally' == $search) {
                    return $this->politicSoldierAtt;
                } elseif ('cargo_ally' == $search) {
                    return $this->politicCargo;
                } elseif ('cost_soldier_ally' == $search) {
                    return $this->politicCostSoldier;
                }
            }
            if ($this->ally->getLevel() >= 4) {
                if ('armor_ally' == $search) {
                    return $this->politicArmor;
                } elseif ('soldier_sale_ally' == $search) {
                    return $this->politicSoldierSale;
                } elseif ('cost_tank_ally' == $search) {
                    return $this->politicCostTank;
                }
            }
            if ($this->ally->getLevel() >= 5) {
                if ('merchant_ally' == $search) {
                    return $this->politicMerchant;
                } elseif ('tank_def_ally' == $search) {
                    return $this->politicTankDef;
                } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'communism') {
                    return $this->politicInvade;
                }
            }
            if ($this->ally->getLevel() >= 6) {
                if ('colonisation_ally' == $search) {
                    return $this->politicColonisation;
                } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'fascism') {
                    return $this->politicInvade;
                } elseif ('pdg_ally' == $search) {
                    return $this->politicPdg;
                }
            }
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getResearchCost($search)
    {
        if('armement_ally' == $search && $this->ally->getPolitic() == 'fascism') {
            return 50000;
        } elseif('armement_ally' == $search && $this->ally->getPolitic() == 'communism') {
            return 100000;
        } elseif ('cost_scientist_ally' == $search) {
            return 100000;
        } elseif ('armor_ally' == $search) {
            return 200000;
        } elseif ('barge_ally' == $search) {
            return 100000;
        } elseif ('cargo_ally' == $search) {
            return 150000;
        } elseif ('colonisation_ally' == $search) {
            return 300000;
        } elseif ('cost_soldier_ally' == $search) {
            return 150000;
        } elseif ('cost_tank_ally' == $search) {
            return 200000;
        } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'fascism') {
            return 300000;
        } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'communism') {
            return 250000;
        } elseif ('merchant_ally' == $search) {
            return 250000;
        } elseif ('pdg_ally' == $search) {
            return 300000;
        } elseif ('prod_ally' == $search) {
            return 25000;
        } elseif ('recycleur_ally' == $search) {
            return 25000;
        } elseif ('search_ally' == $search) {
            return 50000;
        } elseif ('soldier_att_ally' == $search) {
            return 150000;
        } elseif ('soldier_sale_ally' == $search) {
            return 200000;
        } elseif ('tank_def_ally' == $search) {
            return 250000;
        } elseif ('worker_ally' == $search) {
            return 25000;
        } elseif ('worker_def_ally' == $search) {
            return 50000;
        }
        return 0;
    }

    /**
     * @return mixed
     */
    public function getResearchTime($search)
    {
        if('armement_ally' == $search && $this->ally->getPolitic() == 'fascism') {
            return 1200;
        } elseif('armement_ally' == $search && $this->ally->getPolitic() == 'communism') {
            return 1500;
        } elseif ('cost_scientist_ally' == $search) {
            return 1500;
        } elseif ('armor_ally' == $search) {
            return 2100;
        } elseif ('barge_ally' == $search) {
            return 1500;
        } elseif ('cargo_ally' == $search) {
            return 1800;
        } elseif ('colonisation_ally' == $search) {
            return 2700;
        } elseif ('cost_soldier_ally' == $search) {
            return 1800;
        } elseif ('cost_tank_ally' == $search) {
            return 2100;
        } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'fascism') {
            return 2700;
        } elseif ('invade_ally' == $search && $this->ally->getPolitic() == 'communism') {
            return 2400;
        } elseif ('merchant_ally' == $search) {
            return 2400;
        } elseif ('pdg_ally' == $search) {
            return 2700;
        } elseif ('prod_ally' == $search) {
            return 900;
        } elseif ('recycleur_ally' == $search) {
            return 900;
        } elseif ('search_ally' == $search) {
            return 1200;
        } elseif ('soldier_att_ally' == $search) {
            return 1800;
        } elseif ('soldier_sale_ally' == $search) {
            return 2100;
        } elseif ('tank_def_ally' == $search) {
            return 2400;
        } elseif ('worker_ally' == $search) {
            return 900;
        } elseif ('worker_def_ally' == $search) {
            return 1200;
        }
        return 0;
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
    public function getAllTank(): int
    {
        $return = 0;
        foreach($this->planets as $planet) {
            $return = $return + $planet->getTank();
        }
        return $return;
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
     * @return int
     */
    public function getAllTroops(): int
    {
        $return = 0;
        if ($this->politicCostSoldier > 0) {
            $sPrice = 3 / (1 + ($this->politicCostSoldier / 10));
            $sPrices = 6 / ( 1 + ($this->politicCostSoldier / 10));
        } else {
            $sPrice = 3;
            $sPrices = 6;
        }
        if ($this->politicCostTank > 0) {
            $tPrice = 250 / (1 + ($this->politicCostTank / 5));
        } else {
            $tPrice = 250;
        }
        if ($this->politicCostScientist > 0) {
            $scPrice = 50 / (1 + ($this->politicCostScientist / 5));
            $scPrices = 100 / (1 + ($this->politicCostScientist / 5));
        } else {
            $scPrice = 50;
            $scPrices = 100;
        }
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getSoldier() * $sPrice);
            $return = $return + $planet->getSoldierAtNbr();
            $return = $return + ($planet->getTank() * $tPrice);
            $return = $return + ($planet->getTankAtNbr() * $tPrice);
            $return = $return + ($planet->getScientist() * $scPrice);
            $return = $return + ($planet->getScientistAtNbr() * $scPrices);
        }
        foreach($this->fleets as $fleet) {
            $return = $return + ($fleet->getSoldier() * $sPrices);
            $return = $return + ($fleet->getScientist() * $scPrice);
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
    public function getLevel(): int
    {
        $return = 0;
        if ($this->experience < 500) {
            $return = 0;
        } elseif ($this->experience < 2000) {
            $return = 1;
        } elseif ($this->experience < 10000) {
            $return = 2;
        } elseif ($this->experience < 25000) {
            $return = 3;
        } elseif ($this->experience < 40000) {
            $return = 4;
        } elseif ($this->experience < 75000) {
            $return = 5;
        } elseif ($this->experience < 100000) {
            $return = 6;
        } elseif ($this->experience < 150000) {
            $return = 7;
        } elseif ($this->experience < 200000) {
            $return = 8;
        } elseif ($this->experience < 280000) {
            $return = 9;
        } elseif ($this->experience < 350000) {
            $return = 10;
        } elseif ($this->experience < 450000) {
            $return = 11;
        } elseif ($this->experience < 600000) {
            $return = 12;
        } elseif ($this->experience < 800000) {
            $return = 13;
        } elseif ($this->experience < 1000000) {
            $return = 14;
        } elseif ($this->experience < 1300000) {
            $return = 15;
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
        if ($this->ally) {
            $bonus = 1.2 + ($this->politicProd / 14);
        } else {
            $bonus = 1;
        }
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getNbProduction() * 60) * $bonus;
        }
        return $return;
    }

    /**
     * @return int
     */
    public function getAllWater(): int
    {
        $return = 0;
        if ($this->ally) {
            $bonus = 1.2 + ($this->politicProd / 14);
        } else {
            $bonus = 1;
        }
        foreach($this->planets as $planet) {
            $return = $return + ($planet->getWtProduction() * 60) * $bonus;
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
            if ($this->getAlly()->getRadarAlliance($sector, $gal) > $return || $return == null) {
                $return = $this->getAlly()->getRadarAlliance($sector, $gal);
            }
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getSpecUsername()
    {
        $return = null;
        $name = ['Admin', 'Dev', 'Zombies'];

        if(in_array($this->username, $name)) {
            $return = $this->username;
        }
        return $return;
    }

    /**
     * @return mixed
     */
    public function getGalaxyPlanets()
    {
        $return = [];

        if(count($this->getPlanets()) > 0){
            foreach ($this->getPlanets() as $planet) {
                if (!in_array($planet->getSector()->getGalaxy()->getPosition(), $return)) { // fixmr vérifier fonction
                    $return[] = $planet->getSector()->getGalaxy()->getPosition();
                }
            }

            return $return;
        }
    }

    /**
     * @return mixed
     */
    public function getFleetsInList($fleetList)
    {
        $fleets = null;
        foreach($this->fleets as $fleet) {
            if($fleet->getFleetList()) {
                if($fleet->getFleetList()->getId() != $fleetList) {
                    $fleets[] = $fleet;
                }
            } else {
                $fleets[] = $fleet;
            }
        }
        return $fleets;
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
        return ['ROLE_USER'];
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
     * @return mixed
     */
    public function getBarbed()
    {
        return $this->barbed;
    }

    /**
     * @param mixed $barbed
     */
    public function setBarbed($barbed): void
    {
        $this->barbed = $barbed;
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
    public function getExpansion()
    {
        return $this->expansion;
    }

    /**
     * @param mixed $expansion
     */
    public function setExpansion($expansion): void
    {
        $this->expansion = $expansion;
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
     * @return mixed
     */
    public function getCheat()
    {
        return $this->cheat;
    }

    /**
     * @param mixed $cheat
     */
    public function setCheat($cheat): void
    {
        $this->cheat = $cheat;
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
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param mixed $confirmed
     */
    public function setConfirmed($confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return mixed
     */
    public function getNewletter()
    {
        return $this->newletter;
    }

    /**
     * @param mixed $newletter
     */
    public function setNewletter($newletter): void
    {
        $this->newletter = $newletter;
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
    public function getQuests()
    {
        return $this->quests;
    }

    /**
     * @param mixed $quests
     */
    public function setQuests($quests): void
    {
        $this->quests = $quests;
    }

    /**
     * Add quest
     *
     * @param \App\Entity\Quest $quest
     *
     * @return Quest
     */
    public function addQuest(\App\Entity\Quest $quest)
    {
        $this->quests[] = $quest;

        return $this;
    }

    /**
     * Remove quest
     *
     * @param \App\Entity\Quest $quest
     */
    public function removeQuest(\App\Entity\Quest $quest)
    {
        $this->quests->removeElement($quest);
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
    public function getFleetLists()
    {
        return $this->fleetLists;
    }

    /**
     * @param mixed $fleetLists
     */
    public function setFleetLists($fleetLists): void
    {
        $this->fleetLists = $fleetLists;
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
     * @return mixed
     */
    public function getExperience()
    {
        return $this->experience;
    }

    /**
     * @param mixed $experience
     */
    public function setExperience($experience): void
    {
        $this->experience = $experience;
    }

    /**
     * @return mixed
     */
    public function getPoliticCargo()
    {
        return $this->politicCargo;
    }

    /**
     * @param mixed $politicCargo
     */
    public function setPoliticCargo($politicCargo): void
    {
        $this->politicCargo = $politicCargo;
    }

    /**
     * @return mixed
     */
    public function getPoliticRecycleur()
    {
        return $this->politicRecycleur;
    }

    /**
     * @param mixed $politicRecycleur
     */
    public function setPoliticRecycleur($politicRecycleur): void
    {
        $this->politicRecycleur = $politicRecycleur;
    }

    /**
     * @return mixed
     */
    public function getPoliticWorker()
    {
        return $this->politicWorker;
    }

    /**
     * @param mixed $politicWorker
     */
    public function setPoliticWorker($politicWorker): void
    {
        $this->politicWorker = $politicWorker;
    }

    /**
     * @return mixed
     */
    public function getPoliticProd()
    {
        return $this->politicProd;
    }

    /**
     * @param mixed $politicProd
     */
    public function setPoliticProd($politicProd): void
    {
        $this->politicProd = $politicProd;
    }

    /**
     * @return mixed
     */
    public function getPoliticCostSoldier()
    {
        return $this->politicCostSoldier;
    }

    /**
     * @param mixed $politicCostSoldier
     */
    public function setPoliticCostSoldier($politicCostSoldier): void
    {
        $this->politicCostSoldier = $politicCostSoldier;
    }

    /**
     * @return mixed
     */
    public function getPoliticCostScientist()
    {
        return $this->politicCostScientist;
    }

    /**
     * @param mixed $politicCostScientist
     */
    public function setPoliticCostScientist($politicCostScientist): void
    {
        $this->politicCostScientist = $politicCostScientist;
    }

    /**
     * @return mixed
     */
    public function getPoliticCostTank()
    {
        return $this->politicCostTank;
    }

    /**
     * @param mixed $politicCostTank
     */
    public function setPoliticCostTank($politicCostTank): void
    {
        $this->politicCostTank = $politicCostTank;
    }

    /**
     * @return mixed
     */
    public function getPoliticWorkerDef()
    {
        return $this->politicWorkerDef;
    }

    /**
     * @param mixed $politicWorkerDef
     */
    public function setPoliticWorkerDef($politicWorkerDef): void
    {
        $this->politicWorkerDef = $politicWorkerDef;
    }

    /**
     * @return mixed
     */
    public function getPoliticTankDef()
    {
        return $this->politicTankDef;
    }

    /**
     * @param mixed $politicTankDef
     */
    public function setPoliticTankDef($politicTankDef): void
    {
        $this->politicTankDef = $politicTankDef;
    }

    /**
     * @return mixed
     */
    public function getPoliticSoldierAtt()
    {
        return $this->politicSoldierAtt;
    }

    /**
     * @param mixed $politicSoldierAtt
     */
    public function setPoliticSoldierAtt($politicSoldierAtt): void
    {
        $this->politicSoldierAtt = $politicSoldierAtt;
    }

    /**
     * @return mixed
     */
    public function getPoliticArmement()
    {
        return $this->politicArmement;
    }

    /**
     * @param mixed $politicArmement
     */
    public function setPoliticArmement($politicArmement): void
    {
        $this->politicArmement = $politicArmement;
    }

    /**
     * @return mixed
     */
    public function getPoliticArmor()
    {
        return $this->politicArmor;
    }

    /**
     * @param mixed $politicArmor
     */
    public function setPoliticArmor($politicArmor): void
    {
        $this->politicArmor = $politicArmor;
    }

    /**
     * @return mixed
     */
    public function getPoliticColonisation()
    {
        return $this->politicColonisation;
    }

    /**
     * @param mixed $politicColonisation
     */
    public function setPoliticColonisation($politicColonisation): void
    {
        $this->politicColonisation = $politicColonisation;
    }

    /**
     * @return mixed
     */
    public function getPoliticInvade()
    {
        return $this->politicInvade;
    }

    /**
     * @param mixed $politicInvade
     */
    public function setPoliticInvade($politicInvade): void
    {
        $this->politicInvade = $politicInvade;
    }

    /**
     * @return mixed
     */
    public function getPoliticMerchant()
    {
        return $this->politicMerchant;
    }

    /**
     * @param mixed $politicMerchant
     */
    public function setPoliticMerchant($politicMerchant): void
    {
        $this->politicMerchant = $politicMerchant;
    }

    /**
     * @return mixed
     */
    public function getPoliticSearch()
    {
        return $this->politicSearch;
    }

    /**
     * @param mixed $politicSearch
     */
    public function setPoliticSearch($politicSearch): void
    {
        $this->politicSearch = $politicSearch;
    }

    /**
     * @return mixed
     */
    public function getPoliticSoldierSale()
    {
        return $this->politicSoldierSale;
    }

    /**
     * @param mixed $politicSoldierSale
     */
    public function setPoliticSoldierSale($politicSoldierSale): void
    {
        $this->politicSoldierSale = $politicSoldierSale;
    }

    /**
     * @return mixed
     */
    public function getPoliticBarge()
    {
        return $this->politicBarge;
    }

    /**
     * @param mixed $politicBarge
     */
    public function setPoliticBarge($politicBarge): void
    {
        $this->politicBarge = $politicBarge;
    }

    /**
     * @return mixed
     */
    public function getPoliticPdg()
    {
        return $this->politicPdg;
    }

    /**
     * @param mixed $politicPdg
     */
    public function setPoliticPdg($politicPdg): void
    {
        $this->politicPdg = $politicPdg;
    }

    /**
     * @return mixed
     */
    public function getVoteAlly()
    {
        return $this->voteAlly;
    }

    /**
     * @param mixed $voteAlly
     */
    public function setVoteAlly($voteAlly): void
    {
        $this->voteAlly = $voteAlly;
    }

    /**
     * @return mixed
     */
    public function getVoteName()
    {
        return $this->voteName;
    }

    /**
     * @param mixed $voteName
     */
    public function setVoteName($voteName): void
    {
        $this->voteName = $voteName;
    }

    /**
     * @return mixed
     */
    public function getZombie()
    {
        return $this->zombie;
    }

    /**
     * @param mixed $zombie
     */
    public function setZombie($zombie): void
    {
        $this->zombie = $zombie;
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
    public function getDailyConnect()
    {
        return $this->dailyConnect;
    }

    /**
     * @param mixed $dailyConnect
     */
    public function setDailyConnect($dailyConnect): void
    {
        $this->dailyConnect = $dailyConnect;
    }

    /**
     * @return mixed
     */
    public function getZombieAtt()
    {
        return $this->zombieAtt;
    }

    /**
     * @param mixed $zombieAtt
     */
    public function setZombieAtt($zombieAtt): void
    {
        $this->zombieAtt = $zombieAtt;
    }

    /**
     * @return mixed
     */
    public function getZombieAt()
    {
        return $this->zombieAt;
    }

    /**
     * @param mixed $zombieAt
     */
    public function setZombieAt($zombieAt): void
    {
        $this->zombieAt = $zombieAt;
    }

    /**
     * @param mixed $allyBan
     */
    public function setAllyBan($allyBan): void
    {
        $this->allyBan = $allyBan;
    }
}
