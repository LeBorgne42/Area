<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="ship")
 * @ORM\Entity
 */
class Ship
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Fleet", inversedBy="ship", fetch="EXTRA_LAZY")
     */
    protected $fleet;

    /**
     * @ORM\OneToOne(targetEntity="Orbite", inversedBy="ship", fetch="EXTRA_LAZY")
     */
    protected $orbite;

    /**
     * @ORM\OneToOne(targetEntity="Planet", inversedBy="ship", fetch="EXTRA_LAZY")
     */
    protected $planet;

    /**
     * @ORM\OneToOne(targetEntity="Yhip_Sonde", inversedBy="ship", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="sonde_id", referencedColumnName="id")
     */
    protected $sonde;

    /**
     * @ORM\OneToOne(targetEntity="Yhip_Colonizer", inversedBy="ship", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="colonizer_id", referencedColumnName="id")
     */
    protected $colonizer;

    /**
     * @ORM\OneToOne(targetEntity="Yhip_Hunter", inversedBy="ship", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="hunter_id", referencedColumnName="id")
     */
    protected $hunter;

    /**
     * @ORM\OneToOne(targetEntity="Yhip_Fregate", inversedBy="ship", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="fregate_id", referencedColumnName="id")
     */
    protected $fregate;

    /**
     * @return mixed
     */
    public function getFleet()
    {
        return $this->fleet;
    }

    /**
     * @param mixed $fleet
     */
    public function setFleet($fleet): void
    {
        $this->fleet = $fleet;
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

    public function getId()
    {
        return $this->id;
    }
}
