<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="building")
 * @ORM\Entity
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
     * @ORM\OneToOne(targetEntity="Xuilding_SpaceShipyard", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="spaceShip_id", referencedColumnName="id")
     */
    protected $spaceShip;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Search", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="buildSearch_id", referencedColumnName="id")
     */
    protected $buildSearch;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Metropole", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="metropole_id", referencedColumnName="id")
     */
    protected $metropole;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_City", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
    protected $city;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Caserne", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="caserne_id", referencedColumnName="id")
     */
    protected $caserne;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_Radar", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="radar_id", referencedColumnName="id")
     */
    protected $radar;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_SkyRadar", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="skyRadar_id", referencedColumnName="id")
     */
    protected $skyRadar;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_SkyBrouilleur", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="skyBrouilleur_id", referencedColumnName="id")
     */
    protected $skyBrouilleur;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_LightUsine", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="lightUsine_id", referencedColumnName="id")
     */
    protected $lightUsine;

    /**
     * @ORM\OneToOne(targetEntity="Xuilding_HeavyUsine", inversedBy="building", fetch="EXTRA_LAZY", cascade={"persist"})
     * @ORM\JoinColumn(name="heavyUsine_id", referencedColumnName="id")
     */
    protected $heavyUsine;

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

    /**
     * @return mixed
     */
    public function getSpaceShip()
    {
        return $this->spaceShip;
    }

    /**
     * @param mixed $spaceShip
     */
    public function setSpaceShip($spaceShip): void
    {
        $this->spaceShip = $spaceShip;
    }

    /**
     * @return mixed
     */
    public function getBuildSearch()
    {
        return $this->buildSearch;
    }

    /**
     * @param mixed $buildSearch
     */
    public function setBuildSearch($buildSearch): void
    {
        $this->buildSearch = $buildSearch;
    }

    /**
     * @return mixed
     */
    public function getCaserne()
    {
        return $this->caserne;
    }

    /**
     * @param mixed $caserne
     */
    public function setCaserne($caserne): void
    {
        $this->caserne = $caserne;
    }

    /**
     * @return mixed
     */
    public function getRadar()
    {
        return $this->radar;
    }

    /**
     * @param mixed $radar
     */
    public function setRadar($radar): void
    {
        $this->radar = $radar;
    }

    /**
     * @return mixed
     */
    public function getSkyRadar()
    {
        return $this->skyRadar;
    }

    /**
     * @param mixed $skyRadar
     */
    public function setSkyRadar($skyRadar): void
    {
        $this->skyRadar = $skyRadar;
    }

    /**
     * @return mixed
     */
    public function getSkyBrouilleur()
    {
        return $this->skyBrouilleur;
    }

    /**
     * @param mixed $skyBrouilleur
     */
    public function setSkyBrouilleur($skyBrouilleur): void
    {
        $this->skyBrouilleur = $skyBrouilleur;
    }

    /**
     * @return mixed
     */
    public function getLightUsine()
    {
        return $this->lightUsine;
    }

    /**
     * @param mixed $lightUsine
     */
    public function setLightUsine($lightUsine): void
    {
        $this->lightUsine = $lightUsine;
    }

    /**
     * @return mixed
     */
    public function getHeavyUsine()
    {
        return $this->heavyUsine;
    }

    /**
     * @param mixed $heavyUsine
     */
    public function setHeavyUsine($heavyUsine): void
    {
        $this->heavyUsine = $heavyUsine;
    }

    /**
     * @return mixed
     */
    public function getMetropole()
    {
        return $this->metropole;
    }

    /**
     * @param mixed $metropole
     */
    public function setMetropole($metropole): void
    {
        $this->metropole = $metropole;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city): void
    {
        $this->city = $city;
    }

    public function getId()
    {
        return $this->id;
    }
}
