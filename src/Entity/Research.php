<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="research")
 * @ORM\Entity
 */
class Research
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $user;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Onde", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="onde_id", referencedColumnName="id")
     */
    protected $onde;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Industry", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="industry_id", referencedColumnName="id")
     */
    protected $industry;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_LightShip", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="lightShip_id", referencedColumnName="id")
     */
    protected $lightShip;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_HeavyShip", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="heavyShip_id", referencedColumnName="id")
     */
    protected $heavyShip;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Discipline", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="discipline_id", referencedColumnName="id")
     */
    protected $discipline;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Hyperespace", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="hyperespace_id", referencedColumnName="id")
     */
    protected $hyperespace;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Utility", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="utility_id", referencedColumnName="id")
     */
    protected $utility;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Demography", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="demography_id", referencedColumnName="id")
     */
    protected $demography;

    /**
     * @ORM\OneToOne(targetEntity="Zearch_Terraformation", inversedBy="research", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="terraformation_id", referencedColumnName="id")
     */
    protected $terraformation;

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
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
    
    public function getId()
    {
        return $this->id;
    }
}
