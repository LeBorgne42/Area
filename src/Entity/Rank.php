<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="rank")
 * @ORM\Entity
 */
class Rank
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @ORM\OneToOne(targetEntity="User", mappedBy="rank", fetch="EXTRA_LAZY")
     */
    protected $user;

    /**
     * @ORM\Column(name="point",type="bigint")
     */
    protected $point;

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
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param mixed $point
     */
    public function setPoint($point): void
    {
        $this->point = $point;
    }

    public function getId()
    {
        return $this->id;
    }
}
