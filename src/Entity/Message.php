<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="message")
 * @ORM\Entity
 */
class Message
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="messages", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="sender",type="string", nullable=true)
     */
    protected $sender = null;

    /**
     * @ORM\Column(name="idSender",type="integer")
     * @Assert\NotBlank(message = "required")
     */
    protected $idSender = 0;

    /**
     * @ORM\Column(name="title",type="string", length=20)
     * @Assert\NotBlank(message = "required")
     */
    protected $title;

    /**
     * @ORM\Column(name="content",type="string", length=500)
     * @Assert\NotBlank(message = "required")
     */
    protected $content;

    /**
     * @ORM\Column(name="bitcoin",type="bigint")
     */
    protected $bitcoin = 0;

    /**
     * @ORM\Column(name="sendAt",type="datetime")
     */
    protected $sendAt;

    public function getId()
    {
        return $this->id;
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
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @param mixed $sender
     */
    public function setSender($sender): void
    {
        $this->sender = $sender;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getBitcoin()
    {
        return $this->bitcoin;
    }

    /**
     * @param mixed $bitcoin
     */
    public function setBitcoin($bitcoin): void
    {
        $this->bitcoin = $bitcoin;
    }

    /**
     * @return mixed
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @return mixed
     */
    public function getIdSender()
    {
        return $this->idSender;
    }

    /**
     * @param mixed $idSender
     */
    public function setIdSender($idSender): void
    {
        $this->idSender = $idSender;
    }

    /**
     * @param mixed $sendAt
     */
    public function setSendAt($sendAt): void
    {
        $this->sendAt = $sendAt;
    }
}
