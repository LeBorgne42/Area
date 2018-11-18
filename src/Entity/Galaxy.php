<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="galaxy")
 * @ORM\Entity
 */
class Galaxy
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Sector", mappedBy="galaxy", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $sectors;

    /**
     * @ORM\Column(name="position",type="integer")
     * @Assert\NotBlank(message = "required")
     */
    protected $position;

    public function __construct()
    {
        $this->sectors = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getSectors()
    {
        return $this->sectors;
    }

    /**
     * @param mixed $sectors
     */
    public function setSectors($sectors): void
    {
        $this->sectors = $sectors;
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
     * @return mixed
     */
    public function getPlayers()
    {
        $players = 0;
        $criteria = Criteria::create();
        $criteria->where($criteria::expr()->neq('user', null))
            ->andWhere($criteria::expr()->eq('ground', 25))
            ->andWhere($criteria::expr()->eq('sky', 5));

        foreach($this->getSectors() as $sector) {
            $players += count($sector->getPlanets()->matching($criteria));
        }
        return $players;
    }

    /**
     * Add sector
     *
     * @param \App\Entity\Sector $sector
     *
     * @return Galaxy
     */
    public function addSector(\App\Entity\Sector $sector)
    {
        $this->sectors[] = $sector;

        return $this;
    }

    /**
     * Remove sector
     *
     * @param \App\Entity\Sector $sector
     */
    public function removeSector(\App\Entity\Sector $sector)
    {
        $this->sectors->removeElement($sector);
    }

    public function getId()
    {
        return $this->id;
    }
}
