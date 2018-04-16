<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="grade")
 * @ORM\Entity
 */
class Grade
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ally", inversedBy="grades", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ally_id", referencedColumnName="id")
     */
    protected $ally;

    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="grade", fetch="EXTRA_LAZY")
     */
    protected $user;

    /**
     * @ORM\Column(name="placement",type="integer")
     */
    protected $placement;

    /**
     * @ORM\Column(name="name",type="string", length=20)
     * @Assert\NotBlank(message = "required")
     */
    protected $name;

    /**
     * @ORM\Column(name="canRecruit",type="boolean")
     */
    protected $canRecruit = false;

    /**
     * @ORM\Column(name="canKick",type="boolean")
     */
    protected $canKick = false;

    /**
     * @ORM\Column(name="canWar",type="boolean")
     */
    protected $canWar = false;

    /**
     * @ORM\Column(name="canPeace",type="boolean")
     */
    protected $canPeace = false;

    /**
     * @return mixed
     */
    public function getAlly()
    {
        return $this->ally;
    }

    /**
     * @param mixed $ally
     */
    public function setAlly($ally): void
    {
        $this->ally = $ally;
    }

    /**
     * @return mixed
     */
    public function getPlacement()
    {
        return $this->placement;
    }

    /**
     * @param mixed $placement
     */
    public function setPlacement($placement): void
    {
        $this->placement = $placement;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getCanRecruit()
    {
        return $this->canRecruit;
    }

    /**
     * @param mixed $canRecruit
     */
    public function setCanRecruit($canRecruit): void
    {
        $this->canRecruit = $canRecruit;
    }

    /**
     * @return mixed
     */
    public function getCanKick()
    {
        return $this->canKick;
    }

    /**
     * @param mixed $canKick
     */
    public function setCanKick($canKick): void
    {
        $this->canKick = $canKick;
    }

    /**
     * @return mixed
     */
    public function getCanWar()
    {
        return $this->canWar;
    }

    /**
     * @param mixed $canWar
     */
    public function setCanWar($canWar): void
    {
        $this->canWar = $canWar;
    }

    /**
     * @return mixed
     */
    public function getCanPeace()
    {
        return $this->canPeace;
    }

    /**
     * @param mixed $canPeace
     */
    public function setCanPeace($canPeace): void
    {
        $this->canPeace = $canPeace;
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
