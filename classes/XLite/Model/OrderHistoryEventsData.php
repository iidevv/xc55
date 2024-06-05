<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order history event data
 *
 * @ORM\Entity
 * @ORM\Table (name="order_history_event_data",
 *      indexes={
 *          @ORM\Index (name="en", columns={"event_id","name"})
 *      }
 * )
 */
class OrderHistoryEventsData extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Record name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value;

    /**
     * Transaction
     *
     * @var \XLite\Model\Payment\Transaction
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\OrderHistoryEvents", inversedBy="details")
     * @ORM\JoinColumn (name="event_id", referencedColumnName="event_id", onDelete="CASCADE")
     */
    protected $event;


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
     * Set name
     *
     * @param string $name
     * @return OrderHistoryEventsData
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return OrderHistoryEventsData
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set event
     *
     * @param \XLite\Model\OrderHistoryEvents $event
     * @return OrderHistoryEventsData
     */
    public function setEvent(\XLite\Model\OrderHistoryEvents $event = null)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get event
     *
     * @return \XLite\Model\OrderHistoryEvents
     */
    public function getEvent()
    {
        return $this->event;
    }
}
