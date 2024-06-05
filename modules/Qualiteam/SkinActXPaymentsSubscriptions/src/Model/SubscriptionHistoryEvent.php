<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use XLite\Model\AEntity;

/**
 * Subscription
 *
 * @ORM\Entity
 * @ORM\Table (name="subscription_history_events")
 */
class SubscriptionHistoryEvent extends AEntity
{
    const STATUS_SUCCESS = 'S';
    const STATUS_FAILED  = 'F';

    /**
     * Subscription history event unique id
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $eventId;

    /**
     * Event creation timestamp
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $date;

    /**
     * Subscription transaction status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status;

    /**
     * Relation to a subscription entity
     *
     * @var Subscription
     *
     * @ORM\ManyToOne (targetEntity="Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription", inversedBy="events", fetch="LAZY")
     * @ORM\JoinColumn (name="subscription_id", referencedColumnName="id")
     */
    protected $subscription;

    /**
     * Set status
     *
     * @param string $status Status
     *
     * @return void
     */
    public function setStatus($status)
    {
        if (static::STATUS_SUCCESS == $status
            || static::STATUS_FAILED == $status
        ) {
            parent::setStatus($status);
            $this->setDate(Converter::now());
        }
    }

    /**
     * Get eventId
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set date
     *
     * @param integer $date
     * @return SubscriptionHistoryEvent
     */
    public function setDate($date)
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Get date
     *
     * @return integer 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set subscription
     *
     * @param Subscription $subscription
     * @return SubscriptionHistoryEvent
     */
    public function setSubscription(Subscription $subscription = null)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * Get subscription
     *
     * @return Subscription
     */
    public function getSubscription()
    {
        return $this->subscription;
    }
}
