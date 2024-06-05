<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Task
 *
 * @ORM\Entity
 * @ORM\Table (name="tasks")
 */
class Task extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Owner class
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $owner;

    /**
     * Trigger time
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $triggerTime = 0;

    /**
     * Task abstract data
     *
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $data = [];

    /**
     * Owner instance
     *
     * @var \XLite\Core\Task\ATask
     */
    protected $ownerInstance;

    /**
     * Should we start the task
     *
     * @return boolean
     */
    public function isExpired()
    {
        \XLite\Core\Database::getEM()->refresh($this);
        return $this->getTriggerTime() < \XLite\Core\Converter::time()
            || $this->getTriggerTime() == 0;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param string $owner
     * @return Task
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set triggerTime
     *
     * @param integer $triggerTime
     * @return Task
     */
    public function setTriggerTime($triggerTime)
    {
        $this->triggerTime = $triggerTime;
        return $this;
    }

    /**
     * Get triggerTime
     *
     * @return integer
     */
    public function getTriggerTime()
    {
        return $this->triggerTime;
    }

    /**
     * Set data
     *
     * @param array $data
     * @return Task
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}
