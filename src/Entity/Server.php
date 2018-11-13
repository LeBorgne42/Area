<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="server")
 * @ORM\Entity
 */
class Server
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="open",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $open = false;

    /**
     * @ORM\Column(name="nbr_message",type="integer")
     */
    protected $nbrMessage = 0;

    /**
     * @ORM\Column(name="nbr_colonize",type="integer")
     */
    protected $nbrColonize = 0;

    /**
     * @ORM\Column(name="nbr_salon_message",type="integer")
     */
    protected $nbrSalonMessage = 0;

    /**
     * @ORM\Column(name="nbr_invasion",type="integer")
     */
    protected $nbrInvasion = 0;

    /**
     * @ORM\Column(name="nbr_sell",type="integer")
     */
    protected $nbrSell = 0;

    /**
     * @ORM\Column(name="nbr_battle",type="integer")
     */
    protected $nbrBattle = 0;

    /**
     * @ORM\Column(name="nbr_building",type="integer")
     */
    protected $nbrBuilding = 0;

    /**
     * @ORM\Column(name="nbr_research",type="integer")
     */
    protected $nbrResearch = 0;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->nbrMessage = 0;
        $this->nbrColonize = 0;
        $this->nbrSalonMessage = 0;
        $this->nbrInvasion = 0;
        $this->nbrSell = 0;
        $this->nbrBattle = 0;
        $this->nbrBuilding = 0;
        $this->nbrResearch = 0;
    }

    /**
     * @return mixed
     */
    public function getNbrResearch()
    {
        return $this->nbrResearch;
    }

    /**
     * @param mixed $nbrResearch
     */
    public function setNbrResearch($nbrResearch): void
    {
        $this->nbrResearch = $nbrResearch;
    }

    /**
     * @return mixed
     */
    public function getNbrMessage()
    {
        return $this->nbrMessage;
    }

    /**
     * @param mixed $nbrMessage
     */
    public function setNbrMessage($nbrMessage): void
    {
        $this->nbrMessage = $nbrMessage;
    }

    /**
     * @return mixed
     */
    public function getNbrColonize()
    {
        return $this->nbrColonize;
    }

    /**
     * @param mixed $nbrColonize
     */
    public function setNbrColonize($nbrColonize): void
    {
        $this->nbrColonize = $nbrColonize;
    }

    /**
     * @return mixed
     */
    public function getNbrSalonMessage()
    {
        return $this->nbrSalonMessage;
    }

    /**
     * @param mixed $nbrSalonMessage
     */
    public function setNbrSalonMessage($nbrSalonMessage): void
    {
        $this->nbrSalonMessage = $nbrSalonMessage;
    }

    /**
     * @return mixed
     */
    public function getNbrInvasion()
    {
        return $this->nbrInvasion;
    }

    /**
     * @param mixed $nbrInvasion
     */
    public function setNbrInvasion($nbrInvasion): void
    {
        $this->nbrInvasion = $nbrInvasion;
    }

    /**
     * @return mixed
     */
    public function getNbrSell()
    {
        return $this->nbrSell;
    }

    /**
     * @param mixed $nbrSell
     */
    public function setNbrSell($nbrSell): void
    {
        $this->nbrSell = $nbrSell;
    }

    /**
     * @return mixed
     */
    public function getNbrBattle()
    {
        return $this->nbrBattle;
    }

    /**
     * @param mixed $nbrBattle
     */
    public function setNbrBattle($nbrBattle): void
    {
        $this->nbrBattle = $nbrBattle;
    }

    /**
     * @return mixed
     */
    public function getNbrBuilding()
    {
        return $this->nbrBuilding;
    }

    /**
     * @param mixed $nbrBuilding
     */
    public function setNbrBuilding($nbrBuilding): void
    {
        $this->nbrBuilding = $nbrBuilding;
    }

    /**
     * @return mixed
     */
    public function getOpen()
    {
        return $this->open;
    }

    /**
     * @param mixed $open
     */
    public function setOpen($open): void
    {
        $this->open = $open;
    }

    public function getId()
    {
        return $this->id;
    }
}
