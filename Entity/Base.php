<?php

namespace Mrapps\OnesignalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Base {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    protected $weight;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=true)
     */
    protected $updatedAt;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    protected $visible;
    
    public function __construct() {
        $this->createdAt = new \DateTime();
        $this->updatedAt = null;
        $this->visible = 1;
        $this->weight = 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight() {
        return $this->weight;
    }
    
    /**
     * Set weight
     *
     * @param string $weight
     * @return Base
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Base
     */
    public function setCreatedAt($createdAt) {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return Base
     */
    public function setUpdatedAt($updatedAt) {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this->updatedAt;
    }
    
    /**
     * Set visible
     *
     * @param integer $visible
     *
     * @return Base
     */
    public function setVisible($visible) {
        $this->visible = $visible;

        return $this;
    }

    /**
     * Get visible
     *
     * @return \DateTime
     */
    public function getVisible() {
        return $this->visible;
    }
    
    
    /** @ORM\PrePersist */
    public function prePersist() {
        $this->setCreatedAt(new \DateTime());
    }

    /** @ORM\PreUpdate */
    public function preUpdate() {
        $this->setUpdatedAt(new \DateTime());
    }
    
    /**
     * Get Real Updated Date
     *
     * @return \DateTime
     */
    public function getRealUpdatedDate() {
        return ($this->getUpdatedAt() !== null) ? $this->getUpdatedAt() : (($this->getCreatedAt() !== null) ? $this->getCreatedAt() : new \DateTime());
    }
}
