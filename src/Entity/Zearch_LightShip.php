<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="z_lightShip")
 * @ORM\Entity
 */
class Zearch_LightShip
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Research", mappedBy="lightShip", fetch="EXTRA_LAZY")
     */
    protected $research;

    /**
     * @ORM\Column(name="bitcoin",type="integer")
     */
    protected $bitcoin = 2500;

    /**
     * @ORM\Column(name="level",type="integer")
     */
    protected $level = 0;

    /**
     * @ORM\Column(name="finishAt",type="datetime", nullable=true)
     */
    protected $finishAt;

    /**
     * @ORM\Column(name="constructTime",type="bigint")
     */
    protected $constructTime = 2500;

    /**
     * @return mixed
     */
    public function getResearch()
    {
        return $this->research;
    }

    /**
     * @param mixed $research
     */
    public function setResearch($research): void
    {
        $this->research = $research;
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

    public function getId()
    {
        return $this->id;
    }
}
