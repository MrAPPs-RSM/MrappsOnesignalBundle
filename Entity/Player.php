<?php

namespace Mrapps\OnesignalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mrapps\OnesignalBundle\Repository\PlayerRepository")
 * @ORM\Table(name="mrapps_onesignal_players")
 */
class Player extends Base
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="player_id", type="string", length=255)
     */
    protected $playerId;

    /**
     * @var string
     *
     * @ORM\Column(name="device_name", type="string", length=255)
     */
    protected $deviceName;
    
    /**
     * @var string
     *
     * @ORM\Column(name="device_version", type="string", length=255)
     */
    protected $deviceVersion;
    
    /**
     * @var string
     *
     * @ORM\Column(name="platform", type="string", length=255)
     */
    protected $platform;
    
    /**
     * @var string
     *
     * @ORM\Column(name="registration_id", type="string", length=255)
     */
    protected $registrationId;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set playerId
     *
     * @param string $playerId
     *
     * @return Player
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return string
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * Set deviceName
     *
     * @param string $deviceName
     *
     * @return Player
     */
    public function setDeviceName($deviceName)
    {
        $this->deviceName = $deviceName;

        return $this;
    }

    /**
     * Get deviceName
     *
     * @return string
     */
    public function getDeviceName()
    {
        return $this->deviceName;
    }

    /**
     * Set deviceVersion
     *
     * @param string $deviceVersion
     *
     * @return Player
     */
    public function setDeviceVersion($deviceVersion)
    {
        $this->deviceVersion = $deviceVersion;

        return $this;
    }

    /**
     * Get deviceVersion
     *
     * @return string
     */
    public function getDeviceVersion()
    {
        return $this->deviceVersion;
    }

    /**
     * Set platform
     *
     * @param string $platform
     *
     * @return Player
     */
    public function setPlatform($platform)
    {
        $this->platform = $platform;

        return $this;
    }

    /**
     * Get platform
     *
     * @return string
     */
    public function getPlatform()
    {
        return $this->platform;
    }

    /**
     * Set registrationId
     *
     * @param string $registrationId
     *
     * @return Player
     */
    public function setRegistrationId($registrationId)
    {
        $this->registrationId = $registrationId;

        return $this;
    }

    /**
     * Get registrationId
     *
     * @return string
     */
    public function getRegistrationId()
    {
        return $this->registrationId;
    }
}
