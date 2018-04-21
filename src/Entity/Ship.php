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
     * @ORM\ManyToOne(targetEntity="Fleet", inversedBy="ships", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="fleet_id", referencedColumnName="id")
     */
    protected $fleet;

    /**
     * @ORM\ManyToOne(targetEntity="Orbite", inversedBy="ships", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="orbite_id", referencedColumnName="id")
     */
    protected $orbite;

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

    public function getId()
    {
        return $this->id;
    }
}
