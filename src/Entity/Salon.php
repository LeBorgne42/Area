<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="salon")
 * @ORM\Entity
 */
class Salon
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Ally", inversedBy="salon", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="ally_id", referencedColumnName="id")
     */
    protected $ally;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="salons")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="S_Content", mappedBy="salon", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $contents;

    /**
     * @ORM\Column(name="name",type="string", length=20)
     * @Assert\NotBlank(message = "required")
     */
    protected $name;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \App\Entity\User $user
     *
     * @return Salon
     */
    public function addUser(\App\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\User $user
     */
    public function removeUser(\App\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Add content
     *
     * @param \App\Entity\S_Content $content
     *
     * @return Salon
     */
    public function addContent(\App\Entity\S_Content $content)
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param \App\Entity\S_Content $content
     */
    public function removeContent(\App\Entity\S_Content $content)
    {
        $this->contents->removeElement($content);
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
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param mixed $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }

    /**
     * @return mixed
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @param mixed $contents
     */
    public function setContents($contents): void
    {
        $this->contents = $contents;
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

    public function getId()
    {
        return $this->id;
    }
}
