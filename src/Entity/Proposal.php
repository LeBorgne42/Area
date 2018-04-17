<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="proposal")
 * @ORM\Entity
 */
class Proposal
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ally", inversedBy="proposals", fetch="EXTRA_LAZY")
     */
    protected $ally;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="proposals", fetch="EXTRA_LAZY")
     */
    protected $user;

    /**
     * @ORM\Column(name="proposalAt",type="datetime")
     */
    protected $proposalAt;

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

    /**
     * @return mixed
     */
    public function getProposalAt()
    {
        return $this->proposalAt;
    }

    /**
     * @param mixed $proposalAt
     */
    public function setProposalAt($proposalAt): void
    {
        $this->proposalAt = $proposalAt;
    }

    public function getId()
    {
        return $this->id;
    }
}
