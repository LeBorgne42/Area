<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="construction")
 * @ORM\Entity
 */
class Construction
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Planet", inversedBy="constructions", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    protected $planet;

    /**
     * @ORM\Column(name="construct",type="string")
     */
    protected $construct;

    /**
     * @ORM\Column(name="constructTime",type="integer")
     */
    protected $constructTime;

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
    public function getConstructTime()
    {
        return $this->constructTime;
    }

    /**
     * @param mixed $constructTime
     */
    public function setConstructTime($constructTime): void
    {
        $this->constructTime = $constructTime;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
