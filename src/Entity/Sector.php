<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="sector")
 * @ORM\Entity(repositoryClass="App\Repository\ListOrderedRepository")
 */
class Sector
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Planet", mappedBy="sector", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    protected $planet;

    /**
     * @ORM\ManyToOne(targetEntity="Galaxy", inversedBy="sector", fetch="EXTRA_LAZY")
     */
    protected $galaxy;

    /**
     * @ORM\Column(name="position",type="integer")
     * @Assert\NotBlank(message = "required")
     */
    protected $position;

    /**
     * Add planet
     *
     * @param \App\Entity\Planet $planet
     *
     * @return Sector
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
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position): void
    {
        $this->position = $position;
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
    public function getGalaxy()
    {
        return $this->galaxy;
    }

    /**
     * @param mixed $galaxy
     */
    public function setGalaxy($galaxy): void
    {
        $this->galaxy = $galaxy;
    }

    public function getId()
    {
        return $this->id;
    }
}
