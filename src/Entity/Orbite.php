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
     * @ORM\OneToOne(targetEntity="Ship", mappedBy="orbite", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ship_id", referencedColumnName="id")
     */
    protected $ship;

    /**
     * @ORM\OneToOne(targetEntity="Planet", mappedBy="orbite", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $planet;

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
    public function getShip()
    {
        return $this->ship;
    }

    /**
     * @param mixed $ship
     */
    public function setShip($ship): void
    {
        $this->ship = $ship;
    }

    public function getId()
    {
        return $this->id;
    }
}
