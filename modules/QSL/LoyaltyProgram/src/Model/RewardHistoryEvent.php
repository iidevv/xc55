<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Reward History event.
 *
 * @ORM\Entity
 * @ORM\Table (name="reward_history_events")
 * @ORM\HasLifecycleCallbacks
 */
class RewardHistoryEvent extends \XLite\Model\AEntity
{
    /**
     * Event reasons.
     */
    public const EVENT_REASON_ORDER_REWARD    = 'order';
    public const EVENT_REASON_CANCELED_REWARD = 'refund';
    public const EVENT_REASON_REDEEMED_POINTS = 'redeem';
    public const EVENT_REASON_RETURNED_POINTS = 'unredeem';
    public const EVENT_REASON_CUSTOM          = 'custom';
    public const EVENT_REASON_IMPORT          = 'import';
    public const EVENT_REASON_SIGNUP_REWARD   = 'signup';
    public const EVENT_REASON_REVIEW_REWARD   = 'review';
    public const EVENT_REASON_RATING_REWARD   = 'rating';
    public const EVENT_REASON_CANCELED_REVIEW = '-review';
    public const EVENT_REASON_CANCELED_RATING = '-rating';

    /**
     * User-friendly descriptions of event reasons.
     *
     * @var array
     */
    protected static $reasonDescriptions = [
        self::EVENT_REASON_ORDER_REWARD    => 'Reward for purchasing products in order {{order}}',
        self::EVENT_REASON_CANCELED_REWARD => 'Cancel reward for not finished order {{order}}',
        self::EVENT_REASON_REDEEMED_POINTS => 'Use to checkout order {{order}}',
        self::EVENT_REASON_RETURNED_POINTS => 'Return reward points redeemed for not finished order {{order}}',
        self::EVENT_REASON_CUSTOM          => 'Reward points adjusted by the store administrator',
        self::EVENT_REASON_IMPORT          => 'Reward points adjusted by a store administration script',
        self::EVENT_REASON_SIGNUP_REWARD   => 'Reward for creating a store account',
        self::EVENT_REASON_REVIEW_REWARD   => 'Reward for reviewing a product',
        self::EVENT_REASON_RATING_REWARD   => 'Reward for rating a product',
        self::EVENT_REASON_CANCELED_REVIEW => 'Canceled reward for reviewing a product',
        self::EVENT_REASON_CANCELED_RATING => 'Canceled reward for rating a product',
    ];

    /**
     * Event unique ID.
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $event_id;

    /**
     * Event creation timestamp.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $date;

    /**
     * Reward points awarded to the user.
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $points;

    /**
     * Reason for the event.
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $reason;

    /**
     * Event comment.
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $comment = '';

    /**
     * User that the event happened for.
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="rewardEvents", cascade={"merge", "detach"})
     * @ORM\JoinColumn (name="user_id", referencedColumnName="profile_id", nullable=true, onDelete="CASCADE")
     */
    protected $user;

    /**
     * User that initiated the event.
     *
     * @var \XLite\Model\Profile
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Profile", inversedBy="initiatedRewardEvents", cascade={"merge", "detach"})
     * @ORM\JoinColumn (name="initiator_id", referencedColumnName="profile_id", nullable=true, onDelete="CASCADE")
     */
    protected $initiator;

    /**
     * Relation to a order entity.
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="rewardEvents", cascade={"merge", "detach"})
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", nullable=true, onDelete="CASCADE")
     */
    protected $order = null;

    /**
     * Prepare order event before save data operation
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function prepareBeforeSave()
    {
        if (!is_numeric($this->date)) {
            $this->setDate(\XLite\Core\Converter::time());
        }
    }

    /**
     * Get public notes for the event (either the admin's comments, or the event reason explained).
     *
     * @return string
     */
    public function getNotes()
    {
        $comment = $this->getComment();
        $notes   = $comment ?: $this->getReasonDescription();

        $orderNumber = $this->getOrder() ? ('#' . $this->getOrder()->getOrderNumber()) : '';

        return $this->t($notes, ['order' => $orderNumber]);
    }

    /**
     * Return a user-friendly description of the event reason.
     *
     * @return string
     */
    public function getReasonDescription()
    {
        return static::$reasonDescriptions[$this->getReason()] ?? '';
    }

    /**
     * Returns the event identifier.
     *
     * @return integer
     */
    public function getEventId()
    {
        return $this->event_id;
    }

    /**
     * Returns the event date.
     *
     * @return integer
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Sets the event date.
     *
     * @param integer $date Timestamp
     *
     * @return RewardHistoryEvent
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Returns the number of points for the event.
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Sets the number of reward points for the event.
     *
     * @param integer $points Number of points
     *
     * @return RewardHistoryEvent
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Returns the event reason.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Sets the event reason.
     *
     * @param string $reason Short description
     *
     * @return RewardHistoryEvent
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Returns the event comment.
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Sets comments for the event.
     *
     * @param string $comment Short text
     *
     * @return RewardHistoryEvent
     */
    public function setComment($comment)
    {
        $this->comment = $comment;

        return $this;
    }

    /**
     * Returns the user profile associated with the event.
     *
     * @return \XLite\Model\Profile
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Associates a user profile with the event.
     *
     * @param \XLite\Model\Profile $user User profile
     *
     * @return RewardHistoryEvent
     */
    public function setUser(\XLite\Model\Profile $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Returns the profile for the user who initiated the event.
     *
     * @return \XLite\Model\Profile
     */
    public function getInitiator()
    {
        return $this->initiator;
    }

    /**
     * Associates the event with the user who initiated the event.
     *
     * @param \XLite\Model\Profile $initiator User profile
     *
     * @return RewardHistoryEvent
     */
    public function setInitiator(\XLite\Model\Profile $initiator = null)
    {
        $this->initiator = $initiator;

        return $this;
    }

    /**
     * Returns the order for the event.
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Associates an order with the event.
     *
     * @param \XLite\Model\Order $order Order
     *
     * @return RewardHistoryEvent
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;

        return $this;
    }
}
