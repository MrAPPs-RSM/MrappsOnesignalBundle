<?php

namespace Mrapps\OnesignalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Mrapps\OnesignalBundle\Repository\UserPlayerRepository")
 * @ORM\Table(name="mrapps_onesignal_users_players")
 */
class UserPlayer extends Base
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
     * @ORM\ManyToOne(targetEntity="Mrapps\OnesignalBundle\Model\UserInterface")
     * @ORM\JoinColumn(name="user", referencedColumnName="id")
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="player", referencedColumnName="id")
     */
    protected $player;

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
     * Set user
     *
     * @param \Mrapps\OnesignalBundle\Model\UserInterface $user
     *
     * @return UserPlayer
     */
    public function setUser(\Mrapps\OnesignalBundle\Model\UserInterface $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Mrapps\OnesignalBundle\Model\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set player
     *
     * @param \Mrapps\OnesignalBundle\Entity\Player $player
     *
     * @return UserPlayer
     */
    public function setPlayer(\Mrapps\OnesignalBundle\Entity\Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return \Mrapps\OnesignalBundle\Entity\Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
