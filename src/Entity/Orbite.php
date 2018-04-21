<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="orbite")
 * @ORM\Entity(repositoryClass="App\Repository\ListOrderedRepository")
 */
class Orbite
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity="Ship", mappedBy="Orbite", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ship_id", referencedColumnName="id")
     */
    protected $ships;

    /**
     * @ORM\OneToOne(targetEntity="Planet", mappedBy="orbite", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $planet;

    /**
     * Add ship
     *
     * @param \App\Entity\Ship $ship
     *
     * @return Orbite
     */
    public function addShip(\App\Entity\Ship $ship)
    {
        $this->ships[] = $ship;

        return $this;
    }

    /**
     * Remove ship
     *
     * @param \App\Entity\Ship $ship
     */
    public function removeShip(\App\Entity\Ship $ship)
    {
        $this->ships->removeElement($ship);
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

    public function getId()
    {
        return $this->id;
    }
}
