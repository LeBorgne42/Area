<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="building")
 * @ORM\Entity(repositoryClass="App\Repository\ListOrderedRepository")
 */
class Building
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="Planet", mappedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     */
    protected $planet;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Miner", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="miner_id", referencedColumnName="id")
     */
    protected $miner;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Extractor", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="extractor_id", referencedColumnName="id")
     */
    protected $extractor;

    /**
     * @return mixed
     */
    public function getPlanet()
    {
        return $this->planet;
    }

    /**
     * @param mixed $planet
     */
    public function setPlanet($planet): void
    {
        $this->planet = $planet;
    }

    /**
     * @return mixed
     */
    public function getMiner()
    {
        return $this->miner;
    }

    /**
     * @param mixed $miner
     */
    public function setMiner($miner): void
    {
        $this->miner = $miner;
    }

    /**
     * @return mixed
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * @param mixed $extractor
     */
    public function setExtractor($extractor): void
    {
        $this->extractor = $extractor;
    }

    public function getId()
    {
        return $this->id;
    }
}
