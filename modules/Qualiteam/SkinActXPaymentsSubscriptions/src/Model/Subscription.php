<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\Core\CommonCell;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Core\Mailer;
use XLite\Model\OrderItem;
use XLite\Model\Payment\Transaction;
use XLite\Model\Repo\ARepo;

/**
 * Subscription
 *
 * @ORM\Entity
 * @ORM\Table  (name="subscription")
 */
class Subscription extends ASubscriptionPlan
{
    /**
     * Initial order item
     *
     * @var OrderItem
     *
     * @ORM\OneToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="subscription")
     * @ORM\JoinColumn (name="initial_item_id", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $initialOrderItem;

    /**
     * Saved card used for subscription
     *
     * @var XpcTransactionData
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActXPaymentsConnector\Model\Payment\XpcTransactionData")
     * @ORM\JoinColumn (name="card_id", referencedColumnName="id")
     */
    protected $card;

    /**
     * Count of failed tries sequence
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $failedTries = 0;

    /**
     * Count of success tries
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $successTries = 0;

    /**
     * Date of initial order
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $startDate;

    /**
     * Calculated date of next payment (can be in the past if there is failed transactions)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $plannedDate;

    /**
     * Real date of next payment (if in the past - transaction is expired)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $realDate;

    /**
     * Subscription status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_NOT_STARTED;

    /**
     * Old status
     *
     * @var string
     */
    protected $oldStatus = '';

    /**
     * Order events queue
     *
     * @var SubscriptionHistoryEvent[]
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionHistoryEvent", mappedBy="subscription", cascade={"all"})
     */
    protected $events;

    /**
     * Status handlers
     *
     * @var array
     */
    protected static $statusHandlers = [
        self::STATUS_ACTIVE      => [
            self::STATUS_STOPPED  => ['stopped', 'statusChangeNotify'],
            self::STATUS_FAILED   => ['expired'],
            self::STATUS_FINISHED => ['finished'],
        ],
        self::STATUS_STOPPED     => [
            self::STATUS_ACTIVE    => ['active', 'statusChangeNotify'],
            self::STATUS_RESTARTED => ['restart'],
        ],
        self::STATUS_FAILED      => [
            self::STATUS_ACTIVE    => ['active', 'statusChangeNotify'],
            self::STATUS_RESTARTED => ['restart'],
        ],
        self::STATUS_RESTARTED   => [
            self::STATUS_ACTIVE  => ['statusChangeNotify'],
            self::STATUS_STOPPED => ['stopped', 'statusChangeNotify'],
        ],
        self::STATUS_NOT_STARTED => [
            self::STATUS_ACTIVE => ['statusChangeNotify'],
        ],
    ];
    /**
     * Calculate shipping for recurring orders
     *
     * @var bool
     * 
     * @ORM\Column (type="boolean")
     */
    protected $calculate_shipping = 0;

    /**
     * Shipping address used for subscription
     *
     * @var \XLite\Model\Address
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Address")
     * @ORM\JoinColumn (name="shipping_address", referencedColumnName="address_id")
     */
    protected $shipping_address;

    /**
     * Shipping method id
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $shipping_id = 0;

    /**
     * Get start of the day
     *
     * @param integer $time Time
     *
     * @return integer
     */
    protected static function getDayStart($time)
    {
        return Converter::convertTimeToDayStart($time);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->events = new ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->getInitialOrderItem()->getObject();
    }

    /**
     * Get product name
     *
     * @return string
     */
    public function getProductName()
    {
        return $this->getInitialOrderItem()->getName();
    }

    /**
     * Get initial order
     *
     * @return \XLite\Model\Order
     */
    public function getInitialOrder()
    {
        return $this->getInitialOrderItem()->getOrder();
    }

    /**
     * Get initial transaction
     *
     * @return Transaction
     */
    public function getInitialTransaction()
    {
        return $this->getInitialOrder()->getPaymentTransactions()->last();
    }

    /**
     * Set start date
     *
     * @param integer $time Unix timestamp
     *
     * @return void
     */
    public function setStartDate($time)
    {
        $this->startDate = static::getDayStart($time);
    }

    /**
     * Set planned date
     *
     * @param integer $time Unix timestamp
     *
     * @return void
     */
    public function setPlannedDate($time)
    {
        $this->plannedDate = static::getDayStart($time);
    }

    /**
     * Set real date
     *
     * @param integer $time Unix timestamp
     *
     * @return void
     */
    public function setRealDate($time)
    {
        $this->realDate = static::getDayStart($time);
    }

    /**
     * Set status
     *
     * @param string $status Status
     *
     * @return void
     */
    public function setStatus($status)
    {
        $this->oldStatus = $this->getStatus();

        $this->status = $status;

        $statusHandlers = [];

        if (isset(static::$statusHandlers[$this->oldStatus][$status])) {
            $statusHandlers = static::$statusHandlers[$this->oldStatus][$status];
        }

        foreach ($statusHandlers as $handler) {
            if (method_exists($this, 'process' . ucfirst($handler))) {
                $this->{'process' . ucfirst($handler)}();
            }
        }
    }

    /**
     * Add event
     *
     * @param string $status Status
     *
     * @return void
     */
    public function registerEvent($status)
    {
        $event = new SubscriptionHistoryEvent();
        $event->setStatus($status);
        $event->setSubscription($this);

        $this->addEvents($event);
    }

    /**
     * Get possible cards based on profile
     *
     * @return array
     */
    public function getPossibleSavedCards()
    {
        return $this->getInitialOrder()->getOrigProfile()->getSavedCards();
    }

    /**
     * Get order profile
     *
     * @return \XLite\Model\Profile
     */
    public function getProfile()
    {
        return $this->getInitialOrder()->getProfile();
    }

    /**
     * Get original profile
     *
     * @return \XLite\Model\Profile
     */
    public function getOrigProfile()
    {
        return $this->getInitialOrder()->getOrigProfile();
    }

    /**
     * Get remaining payments
     *
     * @return integer
     */
    public function getRemainingPayments()
    {
        return $this->getPeriods()
            ? $this->getPeriods() - $this->getSuccessTries()
            : 0;
    }

    /**
     * getPendingPaymentNumber
     *
     * @return integer
     */
    public function getPendingPaymentNumber()
    {
        return $this->getSuccessTries() + 1;
    }

    /**
     * Get next try date
     *
     * @return integer
     */
    public function getNextTryDate()
    {
        $config = Config::getInstance()->Qualiteam->SkinActXPaymentsSubscriptions;

        return $this->getRealDate() + $config->rebill_attempt_period * static::DAY_IN_SECONDS;
    }

    /**
     * Get last Order ID
     *
     * @return integer
     */
    public function getLastOrderId()
    {
        $cnd = new CommonCell();
        $cnd->{\XLite\Model\Repo\Order::SEARCH_SUBSCRIPTION} = $this;
        $cnd->{ARepo::P_ORDER_BY} = ['o.date', 'DESC'];
        $cnd->{ARepo::P_LIMIT} = [0, 1];

        $order = Database::getRepo(\XLite\Model\Order::class)->search($cnd);

        $result = false;

        if ($order) {
            $order = array_pop($order);
            $result = $order->getOrderId();
        }

        return $result;
    }

    /**
     * Get orders
     *
     * @return array
     */
    public function getOrders()
    {
        $cnd = new CommonCell();
        $cnd->{\XLite\Model\Repo\Order::SEARCH_SUBSCRIPTION} = $this;

        return Database::getRepo('\XLite\Model\Order')->search($cnd);
    }

    /**
     * Update subscription by payment transaction
     *
     * @param Transaction $transaction Transaction
     *
     * @return void
     */
    public function updateByPaymentTransaction(Transaction $transaction)
    {
        $config = Config::getInstance()->Qualiteam->SkinActXPaymentsSubscriptions;

        if (
            $transaction->isAuthorized()
            || $transaction->isCaptured()            
        ) {

            $this->setFailedTries(0);

            $successTries = $this->getSuccessTries() + 1;
            $this->setSuccessTries($successTries);

            // Next date depends on the successful and failed tries, so it should be calculated after the tries done
            $nextDate = $this->getNextDate();
            $this->setRealDate($nextDate);
            $this->setPlannedDate($nextDate);

            Mailer::sendSubscriptionPaymentSuccessful($transaction->getOrder());

            if ($this->getPeriods() == $successTries) {
                $this->setStatus(ASubscriptionPlan::STATUS_FINISHED);
            } elseif (ASubscriptionPlan::STATUS_RESTARTED == $this->getStatus()) {
                // Activate pending restarted subscription on first success
                $this->setStatus(ASubscriptionPlan::STATUS_ACTIVE);
            }

            $this->registerEvent(SubscriptionHistoryEvent::STATUS_SUCCESS);

        } else {

            $failedTries = $this->getFailedTries() + 1;
            $this->setFailedTries($failedTries);

            // Next date depends on the successful and failed tries, so it should be calculated after the tries done
            $nextDate = $this->getNextTryDate();
            $this->setRealDate($nextDate);

            if (
                $config->rebill_attempts == $failedTries
                || ASubscriptionPlan::STATUS_RESTARTED == $this->getStatus()
            ) {
                // Fail pending restarted subscription on first error
                $this->setStatus(ASubscriptionPlan::STATUS_FAILED);
            }

            $this->registerEvent(SubscriptionHistoryEvent::STATUS_FAILED);
        }

        $this->update();

        if (Transaction::STATUS_FAILED === $transaction->getStatus()) {

            // Send notification to customer for the failed payment or for the failed (terminated) subscription
            if (self::STATUS_FAILED !== $this->getStatus()) {
                Mailer::sendSubscriptionPaymentFailed($transaction->getOrder());
            } else {
                Mailer::sendSubscriptionFailed($transaction->getOrder());
            }
        }
    }

    /**
     * Process change status
     *
     * @return void
     */
    protected function processStopped()
    {

    }

    /**
     * Process change status
     *
     * @return void
     */
    protected function processExpired()
    {

    }

    /**
     * Process change status
     *
     * @return void
     */
    protected function processFinished()
    {

    }

    /**
     * Process change status
     *
     * @return void
     */
    protected function processActive()
    {
        if ($this->getPlannedDate() <= static::getDayStart(Converter::now())) {
            $this->setPlannedDate($this->getNextDate(Converter::now()));
        }

        $this->setRealDate($this->getPlannedDate());

        $this->setFailedTries(0);
        
    }

    /**
     * Process subscription restart
     *
     * @return void
     */
    protected function processRestart()
    {
        $now = Converter::now();

        $this->setStartDate($now);

        // To force charge on next cron execution
        $this->setPlannedDate($now);
        $this->setRealDate($now);

        // Decrease total periods and reset success tries
        // so that next date will be calculated properly
        if ($this->getPeriods() > $this->getSuccessTries()) {
            $this->setPeriods($this->getPeriods() - $this->getSuccessTries());
        } elseif ($this->getPeriods() > 0) {
            // Give one more period for finished to avoid bugs
            $this->setPeriods(1);
        }

        $this->setSuccessTries(0);

        $this->setFailedTries(0);
    }

    /**
     * Send notification with subscription status on it's change
     *
     * @return void
     */
    protected function processStatusChangeNotify()
    {
        Mailer::sendSubscriptionStatus($this);
    }

    /**
     * Checks if status is Active
     *
     * @return string 
     */
    public function isActive()
    {
        return $this->getStatus() == ASubscriptionPlan::STATUS_ACTIVE;
    }

    /**
     * Checks if status is Restarted
     *
     * @return string 
     */
    public function isRestarted()
    {
        return $this->getStatus() == ASubscriptionPlan::STATUS_RESTARTED;
    }

    /**
     * Checks if subscription can be restarted
     *
     * @return string 
     */
    public function isRestartable()
    {
        return in_array($this->getStatus(), [ASubscriptionPlan::STATUS_STOPPED, ASubscriptionPlan::STATUS_FAILED]);
    }

    /**
     * Set failedTries
     *
     * @param integer $failedTries
     * @return Subscription
     */
    public function setFailedTries($failedTries)
    {
        $this->failedTries = $failedTries;
        return $this;
    }

    /**
     * Get failedTries
     *
     * @return integer 
     */
    public function getFailedTries()
    {
        return $this->failedTries;
    }

    /**
     * Set successTries
     *
     * @param integer $successTries
     * @return Subscription
     */
    public function setSuccessTries($successTries)
    {
        $this->successTries = $successTries;
        return $this;
    }

    /**
     * Get successTries
     *
     * @return integer 
     */
    public function getSuccessTries()
    {
        return $this->successTries;
    }

    /**
     * Get startDate
     *
     * @return integer 
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Get plannedDate
     *
     * @return integer 
     */
    public function getPlannedDate()
    {
        return $this->plannedDate;
    }

    /**
     * Get realDate
     *
     * @return integer 
     */
    public function getRealDate()
    {
        return $this->realDate;
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Subscription
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return Subscription
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }

    /**
     * Set period
     *
     * @param string $period
     * @return Subscription
     */
    public function setPeriod($period)
    {
        $this->period = $period;
        return $this;
    }

    /**
     * Get period
     *
     * @return string 
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Set reverse
     *
     * @param boolean $reverse
     * @return Subscription
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
        return $this;
    }

    /**
     * Get reverse
     *
     * @return boolean 
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * Set periods
     *
     * @param integer $periods
     * @return Subscription
     */
    public function setPeriods($periods)
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * Get periods
     *
     * @return integer 
     */
    public function getPeriods()
    {
        return $this->periods;
    }

    /**
     * Set fee
     *
     * @param float $fee
     * @return Subscription
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
        return $this;
    }

    /**
     * Get fee
     *
     * @return float 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set initialOrderItem
     *
     * @param OrderItem $initialOrderItem
     * @return Subscription
     */
    public function setInitialOrderItem(OrderItem $initialOrderItem = null)
    {
        $this->initialOrderItem = $initialOrderItem;
        return $this;
    }

    /**
     * Get initialOrderItem
     *
     * @return OrderItem
     */
    public function getInitialOrderItem()
    {
        return $this->initialOrderItem;
    }

    /**
     * Add events
     *
     * @param SubscriptionHistoryEvent $events
     * @return Subscription
     */
    public function addEvents(SubscriptionHistoryEvent $events)
    {
        $this->events[] = $events;
        return $this;
    }

    /**
     * Get events
     *
     * @return Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set card
     *
     * @param XpcTransactionData $card
     * @return Subscription
     */
    public function setXpcData(XpcTransactionData $card = null)
    {
        $this->card = $card;
        return $this;
    }

    /**
     * Checks if this card belongs to the current profile 
     *
     * @return boolean
     */
    public function isCardValid()
    {
        if (!$this->card) {
            return false;
        }

        $cnd = new CommonCell();

        $class = \Qualiteam\SkinActXPaymentsConnector\Model\Repo\Payment\XpcTransactionData::class;

        $cnd->{$class::SEARCH_RECHARGES_ONLY} = true;
        $cnd->{$class::SEARCH_PAYMENT_ACTIVE} = true;
        $cnd->{$class::SEARCH_CARD_ID} = $this->card->getId();

        $valid = Database::getRepo(XpcTransactionData::class)
            ->search($cnd, true);

        return !empty($valid);
    }

    /**
     * Get raw card data
     *
     * @return XpcTransactionData
     */
    public function getXpcData()
    {
        if (!$this->isCardValid()) {
            return null;
        }

        return $this->card;
    }

    /**
     * Get parsed card 
     *
     * @return array
     */
    public function getCard()
    {
        $result = false;

        if ($this->getXpcData()) {

            $result = [
                'card_id'           => $this->getXpcData()->getId(),
                'card_number'       => $this->getXpcData()->getCardNumber(),
                'card_type'         => $this->getXpcData()->getCardType(),
                'card_type_css'     => strtolower($this->getXpcData()->getCardType()),
                'use_for_recharges' => $this->getXpcData()->getUseForRecharges(),
                'expire'            => $this->getXpcData()->getCardExpire(),
            ];
        }

        return $result;
    }

    /**
     * Set value of "Calculate shipping for recurring orders" option for subscription
     *
     * @param boolean
     * @return Subscription
     */
    public function setCalculateShipping($productParam)
    {
        $this->calculate_shipping = $productParam;
        return $this;
    }

    /**
     * Get value of "Calculate shipping for recurring orders" option for subscription
     *
     * @return boolean
     */
    public function getCalculateShipping()
    {
        return $this->calculate_shipping;
    }

    /**
     * Set shipping address for this subscription
     *
     * @param \XLite\Model\Address
     * @return Subscription
     */
    public function setShippingAddress(\XLite\Model\Address $shipping_address = null)
    {
        $this->shipping_address = $shipping_address;
        return $this;
    }

    /**
     * Get shipping address for this subscription
     *
     * @return \XLite\Model\Address
     */
    public function getShippingAddress()
    {
        return $this->shipping_address;
    }

    /**
     * Get addresses of this user
     *
     * @return \XLite\Model\Address[]
    */
    public function getProfileAddresses()
    {
        $addresses = $this->getOrigProfile()->getAddresses()->getValues();

        foreach ($addresses as $key => $address) {
            if (!is_null($this->shipping_address) && $address->equals($this->shipping_address)) {
                unset($addresses[$key]);
            }
        }
        if (!is_null($this->shipping_address)) {
            $addresses[] = $this->shipping_address;
        }

        return $addresses;
    }

    /**
     * Set shipping method id
     *
     * @param integer $shipping_id Shipping method id
     * @return Subscription
     */
    public function setShippingId($shipping_id)
    {
        $this->shipping_id = $shipping_id;
        return $this;
    }

    /**
     * Get shipping id
     *
     * @return Subscription
     */
    public function getShippingId()
    {
        return $this->shipping_id;
    }

    /**
     * Stop subscription
     *
     * @param \XLite\Model\Order $order Order
     * @return void
     */
    public function stopSubscription($order)
    {
        $this->status = self::STATUS_FAILED;
        Mailer::sendSubscriptionFailed($order, 'Shipping method is not available');
    }

    /**
     * Check if last order of this subscription has zero total
     *
     * @return bool
     */
    public function isFree()
    {
        $order = Database::getRepo('\XLite\Model\Order')->find($this->getLastOrderId());

        return $order && \XLite\Model\Order::ORDER_ZERO >= $order->getTotal();
    }
}
