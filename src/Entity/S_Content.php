<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Criteria;

/**
 * @ORM\Table(name="s_content")
 * @ORM\Entity
 */
class S_Content
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="sContents", fetch="EXTRA_LAZY")
     */
    protected $user;

    /**
     * @ORM\Column(name="message",type="string", length=200)
     * @Assert\NotBlank(message = "required")
     */
    protected $message;

    /**
     * @ORM\ManyToOne(targetEntity="Salon", inversedBy="contents", fetch="EXTRA_LAZY")
     */
    protected $salon;

    /**
     * @ORM\Column(name="sendAt",type="datetime")
     */
    protected $sendAt;

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
    public function getMessage()
    {
        if($this->salon->getName() == 'Private') {
            $encrypt_method = "aes256";
            $secret_key = '°)Qfdd:M§¨¨èè!iV2dfgdfg&';
            $secret_iv = '°)!!èQ:Mghfg§¨g¨iV!!dfg&';
            $key = hash('sha256', $secret_key);
            $iv = substr(hash('sha256', $secret_iv), 0, 16);
            $decrypt = openssl_decrypt($this->message, $encrypt_method, $key, false, $iv);
            $decrypt = str_replace('d(kKd-&é°?,/+sSqwX@', '', $decrypt);
            return $decrypt;
        } else {
            return $this->message;
        }
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getSalon()
    {
        return $this->salon;
    }

    /**
     * @param mixed $salon
     */
    public function setSalon($salon): void
    {
        $this->salon = $salon;
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

    public function getId()
    {
        return $this->id;
    }
}
