<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Subscription;

use Doctrine\ORM\Mapping as ORM;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;

/**
 * An entity for storing subscriptions relations with shipping methods and shipping addresses, etc.
 *
 * @ORM\Entity
 * @ORM\Table  (name="xpayments_subscriptions")
 */
class Subscription extends Base\ASubscriptionPlan
{
    /**
     * Unique id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true, "comment": "Unique id" })
     */
    protected $id;

    /**
     * Initial order item
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\OneToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="xpaymentsSubscription")
     * @ORM\JoinColumn (name="initialOrderItem", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $initialOrderItem;

    /**
     * Whether to calculate shipping for recurring orders
     *
     * @var bool
     *
     * @ORM\Column (type="boolean")
     */
    protected $calculateShipping = false;

    /**
     * Shipping address used for subscription
     *
     * @var \XLite\Model\Address
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Address")
     * @ORM\JoinColumn (name="shippingAddress", referencedColumnName="address_id")
     */
    protected $shippingAddress;

    /**
     * Shipping method id
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $shippingId = 0;

    /**
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $xpaymentsSubscriptionPublicId = '';

    /**
     * Subscription status
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $status = self::STATUS_NOT_STARTED;

    /**
     * Count of failed attempts sequence
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $failedAttempts = 0;

    /**
     * Count of successful attempts
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $successfulAttempts = 0;

    /**
     * Date of initial order
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $startDate;

    /**
     * Calculated date of next payment (may be in the past if there is failed transactions)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $plannedDate;

    /**
     * Actual date of next payment (if in the past - transaction is expired)
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $actualDate;

    /**
     * Card id
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16, nullable=true)
     */
    protected $cardId = '';

    /**
     * Old status
     *
     * @var string
     */
    protected $oldStatus = '';

    /**
     * Status handlers
     *
     * @var array
     */
    protected static $statusHandlers = [
        self::STATUS_ACTIVE      => [
            self::STATUS_STOPPED  => ['stopped', 'statusChangeNotify'],
            self::STATUS_FAILED   => ['failed'],
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getXpaymentsSubscriptionPublicId()
    {
        return $this->xpaymentsSubscriptionPublicId;
    }

    /**
     * @param string $xpaymentsSubscriptionPublicId
     *
     * @return Subscription
     */
    public function setXpaymentsSubscriptionPublicId($xpaymentsSubscriptionPublicId)
    {
        $this->xpaymentsSubscriptionPublicId = $xpaymentsSubscriptionPublicId;
        return $this;
    }

    /**
     * Set initialOrderItem
     *
     * @param \XLite\Model\OrderItem $initialOrderItem
     *
     * @return Subscription
     */
    public function setInitialOrderItem(\XLite\Model\OrderItem $initialOrderItem = null)
    {
        $this->initialOrderItem = $initialOrderItem;
        return $this;
    }

    /**
     * Get initialOrderItem
     *
     * @return \XLite\Model\OrderItem
     */
    public function getInitialOrderItem()
    {
        return $this->initialOrderItem;
    }

    /**
     * @return int
     */
    public function getShippingId()
    {
        return $this->shippingId;
    }

    /**
     * @param int $shippingId
     *
     * @return Subscription
     */
    public function setShippingId($shippingId)
    {
        $this->shippingId = $shippingId;
        return $this;
    }

    /**
     * @return \XLite\Model\Address
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param \XLite\Model\Address $shippingAddress
     *
     * @return Subscription
     */
    public function setShippingAddress(\XLite\Model\Address $shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCalculateShipping()
    {
        return $this->calculateShipping;
    }

    /**
     * @param bool $calculateShipping
     *
     * @return Subscription
     */
    public function setCalculateShipping($calculateShipping)
    {
        $this->calculateShipping = $calculateShipping;
        return $this;
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
     * @return Order
     */
    public function getInitialOrder()
    {
        return $this->getInitialOrderItem()->getOrder();
    }

    /**
     * @param string $status
     *
     * @return $this
     * @throws \Exception
     */
    public function setStatus(string $status = self::STATUS_NOT_STARTED)
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

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getFailedAttempts(): int
    {
        return $this->failedAttempts;
    }

    /**
     * @param int $failedAttempts
     *
     * @return Subscription
     */
    public function setFailedAttempts(int $failedAttempts): Subscription
    {
        $this->failedAttempts = $failedAttempts;
        return $this;
    }

    /**
     * @return int
     */
    public function getSuccessfulAttempts(): int
    {
        return $this->successfulAttempts;
    }

    /**
     * @param int $successfulAttempts
     *
     * @return Subscription
     */
    public function setSuccessfulAttempts(int $successfulAttempts): Subscription
    {
        $this->successfulAttempts = $successfulAttempts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     *
     * @return Subscription
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlannedDate()
    {
        return $this->plannedDate;
    }

    /**
     * @param mixed $plannedDate
     *
     * @return Subscription
     */
    public function setPlannedDate($plannedDate)
    {
        $this->plannedDate = $plannedDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getActualDate()
    {
        return $this->actualDate;
    }

    /**
     * @param mixed $actualDate
     *
     * @return Subscription
     */
    public function setActualDate($actualDate)
    {
        $this->actualDate = $actualDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Subscription
     */
    public function setType(string $type): Subscription
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     *
     * @return Subscription
     */
    public function setNumber(int $number): Subscription
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return string
     */
    public function getPeriod(): string
    {
        return $this->period;
    }

    /**
     * @param string $period
     *
     * @return Subscription
     */
    public function setPeriod(string $period): Subscription
    {
        $this->period = $period;
        return $this;
    }

    /**
     * @return bool
     */
    public function isReverse(): bool
    {
        return $this->reverse;
    }

    /**
     * @param bool $reverse
     *
     * @return Subscription
     */
    public function setReverse(bool $reverse): Subscription
    {
        $this->reverse = $reverse;
        return $this;
    }

    /**
     * @return int
     */
    public function getPeriods(): int
    {
        return $this->periods;
    }

    /**
     * @param int $periods
     *
     * @return Subscription
     */
    public function setPeriods(int $periods): Subscription
    {
        $this->periods = $periods;
        return $this;
    }

    /**
     * @return string
     */
    public function getCardId(): string
    {
        return $this->cardId;
    }

    /**
     * @param string $cardId
     * @return array
     */
    public function getCardData(string $cardId)
    {
        $result = [];
        $profileCards = $this->getProfile()->getXpaymentsCards();
        foreach ($profileCards as $card) {
            if ($cardId == $card['cardId']) {
                $result = $card;
                break;
            }
        }

        return $result;
    }

    /**
     * @param string $cardId
     *
     * @return Subscription
     */
    public function setCardId(string $cardId): Subscription
    {
        $this->cardId = $cardId;
        return $this;
    }

    /**
     * @param \XPaymentsCloud\Model\Subscription $xpaymentsSubscription
     *
     * @return Subscription
     */
    public function setDataFromApi($xpaymentsSubscription)
    {
        $this->setXpaymentsSubscriptionPublicId($xpaymentsSubscription->getPublicId())
            ->setType($xpaymentsSubscription->getType())
            ->setNumber($xpaymentsSubscription->getNumber())
            ->setPeriod($xpaymentsSubscription->getPeriod())
            ->setReverse($xpaymentsSubscription->getReverse())
            ->setPeriods($xpaymentsSubscription->getPeriods())
            ->setFee($xpaymentsSubscription->getRecurringAmount())
            ->setCardId($xpaymentsSubscription->getCardId())
            ->setFailedAttempts($xpaymentsSubscription->getFailedAttempts())
            ->setSuccessfulAttempts($xpaymentsSubscription->getSuccessfulAttempts())
            ->setStartDate($xpaymentsSubscription->getStartDate())
            ->setPlannedDate($xpaymentsSubscription->getPlannedDate())
            ->setActualDate($xpaymentsSubscription->getActualDate())
            ->setStatus($xpaymentsSubscription->getStatus());

        return $this;
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
     * @return string
     */
    public static function getCallbackUrl()
    {
        return \XLite::getInstance()->getShopURL(
            \XLite\Core\Converter::buildURL('xpayments_subscriptions_callback', '', [], \XLite::getCustomerScript()),
            \XLite\Core\Config::getInstance()->Security->customer_security
        );
    }

    /**
     * Get last Order ID
     *
     * @return integer
     */
    public function getLastOrderId()
    {
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Order::SEARCH_XPAYMENTS_SUBSCRIPTION} = $this;
        $cnd->{\XLite\Model\Repo\Order::P_ORDER_BY} = ['o.date', 'DESC'];
        $cnd->{\XLite\Model\Repo\Order::P_LIMIT} = [0, 1];

        $order = \XLite\Core\Database::getRepo('\XLite\Model\Order')->search($cnd);

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
        $cnd = new \XLite\Core\CommonCell();
        $cnd->{\XLite\Model\Repo\Order::SEARCH_XPAYMENTS_SUBSCRIPTION} = $this;

        $orders = \XLite\Core\Database::getRepo('\XLite\Model\Order')->search($cnd);

        return $orders;
    }

    /**
     * Stop subscription
     *
     * @param \XLite\Model\Order $order Order
     * @return void
     */
    public function stop($order)
    {
        $this->status = self::STATUS_FAILED;
        \XLite\Core\Mailer::sendXpaymentsSubscriptionFailed($order, $this, 'Shipping method is not available');
    }

    /**
     * Create order based on this subscription
     * @throws \Exception
     */
    public function createOrder()
    {
        $orderItem = $this->createOrderItem();

        if ($orderItem) {
            $order = $this->createOrderObject();

            if ($order) {
                $order->addItem($orderItem);

                if ($this->isCalculateShipping()) {
                    $shippingId = $this->getShippingId();
                    $profile = $order->getProfile();

                    /** @var \XLite\Model\Address $orderShippingAddress */
                    $orderShippingAddress = $order->getShippingAddress();
                    $subscriptionShippingAddress = $this->getShippingAddress();

                    if (!$orderShippingAddress->equals($subscriptionShippingAddress)) {
                        /** @var \XLite\Model\Address $extraAddress */
                        $extraAddress = $subscriptionShippingAddress->cloneEntity();
                        $extraAddress->setProfile($profile);
                        $extraAddress->setIsShipping(true);
                        $orderShippingAddress->setIsShipping(false);
                        \XLite\Core\Database::getEM()->persist($extraAddress);
                        \XLite\Core\Database::getEM()->flush();
                        $profile->setShippingAddress($extraAddress);
                    }

                    $shippingMethod = \XLite\Core\Database::getRepo('XLite\Model\Shipping\Method')
                        ->findOneBy(
                            array(
                                'method_id' => $shippingId,
                                'enabled'   => true,
                            )
                        );

                    $shippingRates = $order->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING')->getRates();

                    $shippingIdsArray = [];
                    foreach ($shippingRates as $rate) {
                        $shippingIdsArray[] = $rate->getMethodId();
                    }

                    if (!is_null($shippingMethod) && in_array($shippingMethod->getMethodId(), $shippingIdsArray)) {
                        $order->renewShippingMethod();
                        $order->setShippingMethodName($shippingMethod->getName());
                        $order->setShippingId($shippingMethod->getMethodId());
                    } else {
                        XPaymentsHelper::log('Shipping method #' . $shippingId . ' for subscription #' . $this->getId()
                                        . ' is not available, select other shipping address');
                        $this->stop($order);
                        $this->update();
                    }

                }

                $order->calculate();
                $order->setPaymentMethod($this->getInitialOrder()->getPaymentMethod());
                $order->setPaymentStatus(\XLite\Model\Order\Status\Payment::STATUS_QUEUED);
                $order->setShippingStatus(\XLite\Model\Order\Status\Shipping::STATUS_NEW);
                $order->markAsOrder();
                $order->update();

                $this->update();
            }

        } else {
            $order = null;
        }

        return $order;
    }

    /**
     * Create orderItem
     *
     * @return \XLite\Model\OrderItem
     */
    protected function createOrderItem()
    {
        $orderItem = null;

        $product = $this->getProduct();

        if ($product) {

            /** @var \XLite\Model\OrderItem $orderItem */
            $orderItem = $this->getInitialOrderItem()->cloneEntity();

            if ($orderItem) {
                $orderItem->setProduct($product);
                $orderItem->setXpaymentsSubscription($this);
                $orderItem->setXpaymentsUniqueId('');
                $orderItem->setPrice($this->getInitialOrderItem()->getXpaymentsDisplayFeePrice());
                $orderItem->setItemNetPrice($this->getInitialOrderItem()->getXpaymentsNetFeePrice());
                $orderItem->calculate();
                $orderItem->create();
            }
        }

        return $orderItem;
    }

    /**
     * Create order object
     *
     * @return \XLite\Model\Order
     *
     * @throws \Exception
     */
    protected function createOrderObject()
    {
        $initialOrder = $this->getInitialOrder();
        $order = null;

        if ($initialOrder) {
            $order = new \XLite\Model\Order;
            $order->setCurrency($initialOrder->getCurrency());
            $order->setOrderNumber(\XLite\Core\Database::getRepo('XLite\Model\Order')->findNextOrderNumber());

            if ($initialOrder->getOrigProfile()) {
                $order->setProfileCopy($initialOrder->getOrigProfile());
            } else {
                $clonedProfile = $initialOrder->getProfile()->cloneEntity();
                $order->setProfile($clonedProfile);
                $clonedProfile->setOrder($order);
            }

            $order->create();
        }

        return $order;
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
     * Get value of "Calculate shipping for recurring orders" option for subscription
     *
     * @return boolean
     */
    public function getCalculateShipping()
    {
        return $this->calculateShipping;
    }

    /**
     * Get addresses of this user
     *
     * @return array of \XLite\Model\Address
     */
    public function getProfileAddresses()
    {
        $addresses = $this->getOrigProfile()->getAddresses()->getValues();

        foreach ($addresses as $key => &$address) {
            if (
                !is_null($this->shippingAddress)
                && $address->equals($this->shippingAddress)
            ) {
                unset($addresses[$key]);
            }
        }
        if (!is_null($this->shippingAddress)) {
            $addresses[] = $this->shippingAddress;
        }

        return $addresses;
    }

    /**
     * Get possible cards based on profile
     *
     * @return array
     */
    public function getXpaymentsCards()
    {
        return $this->getInitialOrder()->getOrigProfile()->getXpaymentsCards();
    }

    /**
     * Check if this subscription has zero fee
     *
     * @return bool
     */
    public function isFree()
    {
        return 0.0 === $this->getFee();
    }

    /**
     * Checks if status is Active
     *
     * @return string
     */
    public function isActive()
    {
        return $this->getStatus() == Subscription::STATUS_ACTIVE;
    }

    /**
     * Checks if status is Restarted
     *
     * @return string
     */
    public function isRestarted()
    {
        return $this->getStatus() == Subscription::STATUS_RESTARTED;
    }

    /**
     * Checks if subscription can be restarted
     *
     * @return string
     */
    public function isRestartable()
    {
        return in_array($this->getStatus(), [Subscription::STATUS_STOPPED, Subscription::STATUS_FAILED]);
    }

    /**
     * getPendingPaymentNumber
     *
     * @return integer
     */
    public function getPendingPaymentNumber()
    {
        return $this->getSuccessfulAttempts() + 1;
    }

    /**
     * Get remaining payments
     *
     * @return integer
     */
    public function getRemainingPayments()
    {
        return $this->getPeriods()
            ? $this->getPeriods() - $this->getSuccessfulAttempts()
            : 0;
    }

    /**
     * Send notification with subscription status on it's change
     *
     * @return void
     */
    public function processStatusChangeNotify()
    {
        \XLite\Core\Mailer::sendXpaymentsSubscriptionStatus($this);
    }

}
