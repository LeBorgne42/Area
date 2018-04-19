<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="lightUsine")
 * @ORM\Entity
 */
class Xuilding_LightUsine
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Building", mappedBy="lightUsine", fetch="EXTRA_LAZY")
     */
    protected $building;

    /**
     * @ORM\Column(name="niobium",type="integer")
     */
    protected $niobium = 2300;

    /**
     * @ORM\Column(name="water",type="integer")
     */
    protected $water = 800;

    /**
     * @ORM\Column(name="level",type="boolean")
     */
    protected $level = false;

    /**
     * @ORM\Column(name="finishAt",type="datetime", nullable=true)
     */
    protected $finishAt;

    /**
     * @ORM\Column(name="constructTime",type="bigint")
     */
    protected $constructTime = 1200;

    /**
     * @ORM\Column(name="ground",type="integer")
     */
    protected $ground = 6;

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
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     */
    public function setLevel($level): void
    {
        $this->level = $level;
    }

    /**
     * @return mixed
     */
    public function getFinishAt()
    {
        return $this->finishAt;
    }

    /**
     * @param mixed $finishAt
     */
    public function setFinishAt($finishAt): void
    {
        $this->finishAt = $finishAt;
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

    public function getId()
    {
        return $this->id;
    }
}
