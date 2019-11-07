<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="nickname_list")
 * @ORM\Entity
 */
class NickName
{
    /**
     *
     * @ORM\Column(name="pseudo", type="string", length=16)
     * @Assert\NotBlank(message = "required")
     */
    protected $pseudo;

    public function __construct()
    {
    }

    /**
     * @return mixed
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo): void
    {
        $this->pseudo = $pseudo;
    }
}