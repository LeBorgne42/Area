<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="ally_allied")
 * @ORM\Entity
 */
class Allied
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ally", inversedBy="allieds", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="ally_id", referencedColumnName="id")
     */
    protected $ally;

    /**
     * @ORM\Column(name="allyTag",type="string", length=5)
     * @Assert\NotBlank(message = "required")
     */
    protected $allyTag;

    /**
     * @ORM\Column(name="signedAt",type="datetime")
     */
    protected $signedAt;

    /**
     * @ORM\Column(name="dismissAt",type="datetime", nullable=true)
     */
    protected $dismissAt;

    /**
     * @ORM\Column(name="dismissBy",type="string", length=5, nullable=true)
     */
    protected $dismissBy;

    /**
     * @ORM\Column(name="accepted",type="boolean")
     * @Assert\NotBlank(message = "required")
     */
    protected $accepted;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->accepted = false;
    }

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
    public function getAllyTag()
    {
        return $this->allyTag;
    }

    /**
     * @param mixed $allyTag
     */
    public function setAllyTag($allyTag): void
    {
        $this->allyTag = $allyTag;
    }

    /**
     * @return mixed
     */
    public function getSignedAt()
    {
        return $this->signedAt;
    }

    /**
     * @param mixed $signedAt
     */
    public function setSignedAt($signedAt): void
    {
        $this->signedAt = $signedAt;
    }

    /**
     * @return mixed
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * @param mixed $accepted
     */
    public function setAccepted($accepted): void
    {
        $this->accepted = $accepted;
    }

    /**
     * @return mixed
     */
    public function getDismissAt()
    {
        return $this->dismissAt;
    }

    /**
     * @param mixed $dismissAt
     */
    public function setDismissAt($dismissAt): void
    {
        $this->dismissAt = $dismissAt;
    }

    /**
     * @return mixed
     */
    public function getDismissBy()
    {
        return $this->dismissBy;
    }

    /**
     * @param mixed $dismissBy
     */
    public function setDismissBy($dismissBy): void
    {
        $this->dismissBy = $dismissBy;
    }

    public function getId()
    {
        return $this->id;
    }
}
