<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="sector")
 * @ORM\Entity
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
     * @ORM\OneToMany(targetEntity="Planet", mappedBy="sector", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="planet_id", referencedColumnName="id")
     */
    protected $planets;

    /**
     * @ORM\OneToMany(targetEntity="Fleet", mappedBy="sector", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="fleet_id", referencedColumnName="id")
     */
    protected $fleets;

    /**
     * @ORM\ManyToOne(targetEntity="Galaxy", inversedBy="sectors", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="galaxy_id", referencedColumnName="id")
     */
    protected $galaxy;

    /**
     * @ORM\Column(name="position",type="smallint")
     * @Assert\NotBlank(message = "required")
     */
    protected $position;

    /**
     * @ORM\Column(name="destroy",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $destroy;

    public function __construct()
    {
        $this->planets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->fleets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->destroy = false;
    }

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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPlanets()
    {
        $criteria = Criteria::create()
            ->orderBy(array('position' => 'ASC'));

        return $this->planets->matching($criteria);
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
     * Add fleet
     *
     * @param \App\Entity\Fleet $fleet
     *
     * @return Sector
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

    /**
     * @return mixed
     */
    public function getDestroy()
    {
        return $this->destroy;
    }

    /**
     * @param mixed $destroy
     */
    public function setDestroy($destroy): void
    {
        $this->destroy = $destroy;
    }
}
