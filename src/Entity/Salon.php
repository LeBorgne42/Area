<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToMany(targetEntity="Alliance", inversedBy="salons")
     */
    protected $allys;

    /**
     * @ORM\ManyToMany(targetEntity="Commander", inversedBy="salons")
     */
    private $commanders;

    /**
     * @ORM\OneToMany(targetEntity="S_Content", mappedBy="salon", fetch="EXTRA_LAZY")
     */
    protected $contents;

    /**
     * @ORM\ManyToOne(targetEntity="Server", inversedBy="salons", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="server_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $server;

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
     * Salon constructor.
     * @param string $name
     * @param Server $server
     */
    public function __construct(string $name, Server $server)
    {
        $this->name = $name;
        $this->server = $server;
        $this->commanders = new ArrayCollection();
        $this->allys = new ArrayCollection();
        $this->contents = new ArrayCollection();
        $this->views = new ArrayCollection();
    }

    /**
     * Add commander
     *
     * @param Commander $commander
     *
     * @return Salon
     */
    public function addCommander(Commander $commander): Salon
    {
        $this->commanders[] = $commander;

        return $this;
    }

    /**
     * Remove commander
     *
     * @param Commander $commander
     */
    public function removeCommander(Commander $commander)
    {
        $this->commanders->removeElement($commander);
    }

    /**
     * Add view
     *
     * @param View $view
     *
     * @return Salon
     */
    public function addView(View $view): Salon
    {
        $this->views[] = $view;

        return $this;
    }

    /**
     * Remove view
     *
     * @param View $view
     */
    public function removeView(View $view)
    {
        $this->views->removeElement($view);
    }

    /**
     * Add content
     *
     * @param S_Content $content
     *
     * @return Salon
     */
    public function addContent(S_Content $content): Salon
    {
        $this->contents[] = $content;

        return $this;
    }

    /**
     * Remove content
     *
     * @param S_Content $content
     */
    public function removeContent(S_Content $content)
    {
        $this->contents->removeElement($content);
    }

    /**
     * Add ally
     *
     * @param Alliance $ally
     *
     * @return Salon
     */
    public function addAlliance(Alliance $ally): Salon
    {
        $this->allys[] = $ally;

        return $this;
    }

    /**
     * Remove ally
     *
     * @param Alliance $ally
     */
    public function removeAlliance(Alliance $ally)
    {
        $this->allys->removeElement($ally);
    }

    /**
     * @return mixed
     */
    public function getAlliances()
    {
        return $this->allys;
    }

    /**
     * @param mixed $allys
     */
    public function setAlliances($allys): void
    {
        $this->allys = $allys;
    }

    /**
     * @return mixed
     */
    public function getCommanders()
    {
        return $this->commanders;
    }

    /**
     * @param mixed $commanders
     */
    public function setCommanders($commanders): void
    {
        $this->commanders = $commanders;
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
     * @return string
     */
    public function getName(): string
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

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->server;
    }

    /**
     * @param mixed $server
     */
    public function setServer($server): void
    {
        $this->server = $server;
    }
}
