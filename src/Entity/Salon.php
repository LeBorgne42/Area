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
     * @ORM\ManyToMany(targetEntity="Ally", inversedBy="salons")
     */
    protected $allys;

    /**
     * @ORM\ManyToMany(targetEntity="User", inversedBy="salons")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity="S_Content", mappedBy="salon", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $contents;

    /**
     * @ORM\Column(name="name",type="string", length=30)
     * @Assert\NotBlank(message = "required")
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="View", mappedBy="salon", fetch="EXTRA_LAZY")
     */
    protected $views;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->allys = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contents = new \Doctrine\Common\Collections\ArrayCollection();
        $this->views = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add view
     *
     * @param \App\Entity\View $view
     *
     * @return Salon
     */
    public function addView(\App\Entity\View $view)
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * Remove view
     *
     * @param \App\Entity\View $view
     */
    public function removeView(\App\Entity\View $view)
    {
        $this->views->removeElement($view);
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
     * Add ally
     *
     * @param \App\Entity\Ally $ally
     *
     * @return Salon
     */
    public function addAlly(\App\Entity\Ally $ally)
    {
        $this->allys[] = $ally;

        return $this;
    }

    /**
     * Remove ally
     *
     * @param \App\Entity\Ally $ally
     */
    public function removeAlly(\App\Entity\Ally $ally)
    {
        $this->allys->removeElement($ally);
    }

    /**
     * @return mixed
     */
    public function getAllys()
    {
        return $this->allys;
    }

    /**
     * @param mixed $allys
     */
    public function setAllys($allys): void
    {
        $this->allys = $allys;
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

    /**
     * @return mixed
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * @param mixed $views
     */
    public function setViews($views): void
    {
        $this->views = $views;
    }

    public function getId()
    {
        return $this->id;
    }
}
