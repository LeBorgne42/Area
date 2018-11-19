<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="report")
 * @ORM\Entity
 */
class Report
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reports", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(name="title",type="string", length=50)
     * @Assert\NotBlank(message = "required")
     */
    protected $title;

    /**
     * @ORM\Column(name="newReport",type="boolean")
     */
    protected $newReport = true;

    /**
     * @ORM\Column(name="content",type="string", length=12000)
     * @Assert\NotBlank(message = "required")
     */
    protected $content;

    /**
     * @ORM\Column(name="share_key",type="string", length=20, nullable=true)
     */
    protected $shareKey;

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
    public function getShareKey()
    {
        return $this->shareKey;
    }

    /**
     * @param mixed $shareKey
     */
    public function setShareKey($shareKey): void
    {
        $this->shareKey = $shareKey;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param $content
     */
    public function setContent($content): void
    {
        $this->content = $content;
    }

    /**
     * @return mixed
     */
    public function getSendAt()
    {
        return $this->sendAt;
    }

    /**
     * @param mixed $sendAt
     */
    public function setSendAt($sendAt): void
    {
        $this->sendAt = $sendAt;
    }

    /**
     * @return mixed
     */
    public function getNewReport()
    {
        return $this->newReport;
    }

    /**
     * @param mixed $newReport
     */
    public function setNewReport($newReport): void
    {
        $this->newReport = $newReport;
    }
}
