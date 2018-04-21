<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="fleet")
 * @ORM\Entity
 */
class Fleet
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="fleets", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="Ship", mappedBy="fleet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ship_id", referencedColumnName="id")
     */
    protected $ships;

    /**
     * @ORM\OneToOne(targetEntity="Soldier", inversedBy="fleet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="soldier_id", referencedColumnName="id")
     */
    protected $soldier;

    /**
     * @ORM\OneToOne(targetEntity="Worker", inversedBy="fleet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="worker_id", referencedColumnName="id")
     */
    protected $worker;

    /**
     * @ORM\OneToOne(targetEntity="Scientist", inversedBy="fleet", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="scientist_id", referencedColumnName="id")
     */
    protected $scientist;

    /**
     * Add ship
     *
     * @param \App\Entity\Ship $ship
     *
     * @return Fleet
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
     * @param mixed $soldier
     */
    public function setSoldier($soldier): void
    {
        $this->soldier = $soldier;
    }

    /**
     * @return mixed
     */
    public function getSoldier()
    {
        return $this->soldier;
    }

    /**
     * @param mixed $scientist
     */
    public function setScientist($scientist): void
    {
        $this->scientist = $scientist;
    }

    /**
     * @return mixed
     */
    public function getScientist()
    {
        return $this->scientist;
    }

    /**
     * @return mixed
     */
    public function getWorker()
    {
        return $this->worker;
    }

    /**
     * @param mixed $worker
     */
    public function setWorker($worker): void
    {
        $this->worker = $worker;
    }

    /**
     * @return mixed
     */
    public function getShips()
    {
        return $this->ships;
    }

    /**
     * @param mixed $ships
     */
    public function setShips($ships): void
    {
        $this->ships = $ships;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function getId()
    {
        return $this->id;
    }
}
