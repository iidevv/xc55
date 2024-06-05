<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;
use XLite\Model\Order\Status\Payment as PaymentStatus;
use XLite\Model\Payment\BackendTransaction;
use XLite\Model\Payment\Transaction;
use XPay\XPaymentsCloud\Model\Subscription\Subscription;
use XPay\XPaymentsCloud\Model\Payment\XpaymentsFraudCheckData as FraudCheckData;
use XPay\XPaymentsCloud\Main as XPaymentsHelper;
use XPay\XPaymentsCloud\Core\Wallets as XPaymentsWallets;

/**
 * X-Payments Specific order fields
 *
 * @ORM\Table (
 *   indexes={
 *      @ORM\Index (name="xpaymentsBuyWithWallet", columns={"xpaymentsBuyWithWallet"}),
 *   }
 * )
 *
 * @Extender\Mixin
 */
abstract class Order extends \XLite\Model\Order implements \XLite\Base\IDecorator
{
    /**
     * Fraud statuses
     */
    const FRAUD_STATUS_CLEAN    = 'Clean';
    const FRAUD_STATUS_FRAUD    = 'Fraud';
    const FRAUD_STATUS_REVIEW   = 'Review';
    const FRAUD_STATUS_ERROR    = 'Error';
    const FRAUD_STATUS_UNKNOWN  = '';

    /**
     * Order fraud status
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $xpaymentsFraudStatus = '';

    /**
     * Order fraud type (which system considered the transaction fraudulent)
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $xpaymentsFraudType = '';

    /**
     * Transaction with fraud check data
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $xpaymentsFraudCheckTransactionId = 0;

    /**
     * Flag to mark carts created for Buy With Apple Pay/Google Pay/etc feature
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $xpaymentsBuyWithWallet = '';

    /**
     * @var string
     *
     * @ORM\Column (type="string", length=10)
     */
    protected $delayedPaymentSavedCardId = '';

    /**
     * Fraud check data from transaction
     */
    protected $xpaymentsFraudCheckData = false;

    /**
     * Hash for X-Payments cards
     */
    protected $xpaymentsCards = null;

    /**
     * @param string $value
     *
     * @return Order
     */
    public function markAsBuyWithWallet($value)
    {
        $this->xpaymentsBuyWithWallet = $value;
        return $this;
    }

    /**
     * Checks if this cart is "Buy With" for specific wallet ID
     *
     * @param $walletId
     *
     * @return bool
     */
    public function isBuyWithWallet($walletId)
    {
        return ($walletId == $this->xpaymentsBuyWithWallet);
    }

    /**
     * @return string
     */
    public function getXpaymentsFraudStatus()
    {
        return $this->xpaymentsFraudStatus;
    }

    /**
     * @param string $xpaymentsFraudStatus
     *
     * @return Order
     */
    public function setXpaymentsFraudStatus($xpaymentsFraudStatus)
    {
        $this->xpaymentsFraudStatus = $xpaymentsFraudStatus;
        return $this;
    }

    /**
     * @return string
     */
    public function getXpaymentsFraudType()
    {
        return $this->xpaymentsFraudType;
    }

    /**
     * @param string $xpaymentsFraudType
     *
     * @return Order
     */
    public function setXpaymentsFraudType($xpaymentsFraudType)
    {
        $this->xpaymentsFraudType = $xpaymentsFraudType;
        return $this;
    }

    /**
     * @return int
     */
    public function getXpaymentsFraudCheckTransactionId()
    {
        return $this->xpaymentsFraudCheckTransactionId;
    }

    /**
     * @param int $xpaymentsFraudCheckTransactionId
     *
     * @return Order
     */
    public function setXpaymentsFraudCheckTransactionId($xpaymentsFraudCheckTransactionId)
    {
        $this->xpaymentsFraudCheckTransactionId = $xpaymentsFraudCheckTransactionId;
        return $this;
    }

    /**
     * Get delayedPaymentSavedCardId
     *
     * @return string
     */
    public function getDelayedPaymentSavedCardId()
    {
        return $this->delayedPaymentSavedCardId;
    }

    /**
     * Set delayedPaymentSavedCardId
     *
     * @param $delayedPaymentSavedCardId
     *
     * @return $this
     */
    public function setDelayedPaymentSavedCardId($delayedPaymentSavedCardId = '')
    {
        $this->delayedPaymentSavedCardId = $delayedPaymentSavedCardId;

        return $this;
    }

    /**
     * Get fraud check data from transaction
     *
     * @return array
     */
    public function getXpaymentsFraudCheckData()
    {
        if (empty($this->xpaymentsFraudCheckData)) {

            if ($this->getXpaymentsFraudCheckTransactionId()) {

                $transaction = \XLite\Core\Database::getRepo('XLite\Model\Payment\Transaction')->find(
                    $this->getXpaymentsFraudCheckTransactionId()
                );

                if ($transaction) {
                    $this->xpaymentsFraudCheckData = $transaction->getXpaymentsFraudCheckData();
                }
            }
        }

        return $this->xpaymentsFraudCheckData;
    }

    /**
     * Return anchor name for the information about fraud check on the order details page
     *
     * @return string
     */
    public function getXpaymentsFraudInfoAnchor()
    {
        return 'fraud-info-' . $this->getXpaymentsFraudType();
    }

    /**
     * Is order fraud
     *
     * @return bool
     */
    public function isFraudStatus()
    {
        $result = false;

        $fraudStatuses = array( 
            FraudCheckData::RESULT_MANUAL,
            FraudCheckData::RESULT_PENDING,
            FraudCheckData::RESULT_FAIL,
        );

        $fraudCheckData = $this->getXpaymentsFraudCheckData();

        if ($fraudCheckData) {
            foreach ($fraudCheckData as $item) {
                if (in_array($item->getResult(), $fraudStatuses)) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Get X-Payments cards
     *
     * @return array
     */
    public function getXpaymentsCards()
    {
        if (null === $this->xpaymentsCards) {

            $transactions = $this->getPaymentTransactions();

            $this->xpaymentsCards = array();

            $adminUrl = XPaymentsHelper::getClient()->getAdminUrl();

            foreach ($transactions as $transaction) {

                if (
                    !$transaction->isXpayments()
                    || !$transaction->getDataCell('xpaymentsCardNumber')
                    || !$transaction->getDataCell('xpaymentsCardType')
                    || !$transaction->getDataCell('xpaymentsCardExpirationDate')
                ) {
                    continue;
                }

                $card = array(
                    'cardNumber' => $transaction->getDataCell('xpaymentsCardNumber')->getValue(),
                    'cardType'   => $transaction->getDataCell('xpaymentsCardType')->getValue(),
                    'expire'     => $transaction->getDataCell('xpaymentsCardExpirationDate')->getValue(),
                    'xpid'       => $transaction->getXpaymentsId(),
                    'url'        => $adminUrl . '?target=payment&xpid=' . $transaction->getXpaymentsId(),
                );

                $this->xpaymentsCards[] = $card;
            }
        }

        return $this->xpaymentsCards;
    }

    /**
     * Get calculated payment status
     *
     * @param boolean $override Override calculation cache OPTIONAL
     *
     * @return string
     */
    public function getCalculatedPaymentStatus($override = false)
    {
        $result = parent::getCalculatedPaymentStatus($override);

        if ($this->isXpayments()) {
            /** @var Transaction $lastTransaction */
            $lastTransaction = $this->getPaymentTransactions()->last();

            if (PaymentStatus::STATUS_QUEUED == $result) {

                $backendTransactions = $lastTransaction->getBackendTransactions();
                if (
                    1 == count($backendTransactions)
                    && BackendTransaction::TRAN_TYPE_ACCEPT == $backendTransactions->last()->getType()
                ) {
                    $result = $lastTransaction->getOrder()->getPaymentStatus();
                }

            } elseif (PaymentStatus::STATUS_PAID == $result) {
                // Parent function automatically assumes zero orders as paid
                // We need to check last transaction status to set valid result
                $total = $this->getCurrency()->roundValue($this->getTotal());

                if (
                    static::ORDER_ZERO >= $total
                    && $this->hasOnlyTrialSubscriptionItems()
                    && !$lastTransaction->isCompleted()
                ) {
                    $result = \XLite\Model\Order\Status\Payment::STATUS_DECLINED;
                }
            }

        }

        return $result;
    }

    /**
     * Check - order is payed or not
     * Payed - order has not open total and all payment transactions are failed or completed
     *
     * @return boolean
     */
    public function isPayed()
    {
        $result = parent::isPayed();

        if (
            $result
            && $this->isXpayments()
        ) {
            $total = $this->getCurrency()->roundValue($this->getTotal());
            if (
                static::ORDER_ZERO >= $total
                && $this->hasOnlyTrialSubscriptionItems()
            ) {
                $result = $this->getPaymentTransactions()->last()->isCompleted();
            }
        }

        return $result;
    }

    /**
     * Process backordered items
     *
     * @return int
     *
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    protected function processBackorderedItems()
    {
        if (
            $this->getItems()
            && $this->getItems()->last()
            && $this->getItems()->last()->isXpaymentsEmulated()
        ) {
            $result = 0;
        } else {
            $result = parent::processBackorderedItems();

        }

        return $result;
    }

    /**
     * Difference in order Total after AOM changes if (any)
     *
     * @return float
     */
    public function getAomTotalDifference()
    {
        return $this->getOpenTotal();
    }

    /**
     * Check if total difference after AOM changes is greater than zero
     *
     * @return bool
     */
    public function isAomTotalDifferencePositive()
    {
        return $this->getAomTotalDifference() > \XLite\Model\Order::ORDER_ZERO;
    }

    /**
     * Return array of active X-Payments saved cards of order customer's profile
     *
     * @return array
     */
    public function getActiveXpaymentsCards()
    {
        $cards = array();

        if (
            $this->getOrigProfile()
            && $this->getOrigProfile()->getXpaymentsCards()
        ) {
            $cards = $this->getOrigProfile()->getXpaymentsCards();
            foreach ($cards as $key => $value) {
                if (false === $cards[$key]['isActive']) {
                    unset($cards[$key]);
                }
            }
        }

        return $cards;
    }

    /**
     * Checks if at least one transaction is handled by X-Payments
     *
     * @return bool
     */
    protected function isXpayments()
    {
        $transactions = $this->getPaymentTransactions();

        $isXpayments = false;
        foreach ($transactions as $t) {
            if ($t->isXpayments()) {
                $isXpayments = true;
                break;
            }
        }

        return $isXpayments;
    }

    /**
     * Whether charge the difference is available for the order
     *
     * @return bool
     */
    public function isXpaymentsChargeDifferenceAvailable()
    {
        return $this->isXpayments()
            && $this->isAomTotalDifferencePositive()
            && !empty($this->getActiveXpaymentsCards());
    }

    /**
     * @return array
     */
    public function getPaymentTransactionSums()
    {
        $paymentTransactionSums = parent::getPaymentTransactionSums();

        if ($this->isXpaymentsChargeDifferenceAvailable()) {
            if ($this->getDelayedPaymentSavedCardId()) {
                $text = 'The amount of payment for the order';
            } else {
                $text = 'Difference between total and paid amount';
            }
            $difference = (string) static::t($text);
            $paymentTransactionSums[$difference] = $this->getAomTotalDifference();
        }

        return $paymentTransactionSums;
    }

    /**
     * Exclude Apple Pay from the list of available for checkout payment methods
     * if browser can't support it at all
     *
     * @return array
     */
    public function getPaymentMethods()
    {
        $list = parent::getPaymentMethods();

        foreach ($list as $key => $method) {
            if ($method->isXpaymentsApplePay()) {
                if (
                    !XPaymentsWallets::isBrowserMaySupportApplePay()
                    || XPaymentsHelper::isDelayedPaymentEnabled()
                ) {
                    unset($list[$key]);
                    $transaction = $this->getFirstOpenPaymentTransaction();
                    $cartMethod = $transaction ? $transaction->getPaymentMethod() : null;
                    if (
                        $cartMethod
                        && $method->getServiceName() == $cartMethod->getServiceName()
                    ) {
                        $transaction->setPaymentMethod(null);
                    }
                }
                break;
            } elseif (
                $this->hasXpaymentsSubscriptions()
                && XPaymentsHelper::isDelayedPaymentEnabled()
            ) {
                unset($list[$key]);
                break;
            }
        }

        if (
            empty($list)
            && $this->hasOnlyTrialSubscriptionItems()
        ) {
            $list[] = XPaymentsHelper::getPaymentMethod();
        }

        return $list;
    }

    /**
     * Detach saved card id if the order is cancelled
     *
     * @param mixed $paymentStatus
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        parent::setPaymentStatus($paymentStatus);

        $status = \XLite\Core\Database::getRepo(PaymentStatus::class)->find($paymentStatus);

        if (
            $status
            && PaymentStatus::STATUS_CANCELED == $status->getCode()
            && $this->getDelayedPaymentSavedCardId()
        ) {
            $this->setDelayedPaymentSavedCardId();
        }
    }

    /**
     * Get open (not-payed) total
     *
     * @return float
     */
    public function getOpenTotal()
    {
        $isPaymentReturn = \XLite::getController() instanceof \XLite\Controller\Customer\Checkout
            && 'return' === \XLite::getController()->getAction();

        return $this->isXpayments() && XPaymentsHelper::isDelayedPaymentEnabled() && $isPaymentReturn
            ? 0.00
            : parent::getOpenTotal();
    }

    // X-Payments subscriptions methods

    /**
     * Check if order has subscriptions
     *
     * @return boolean
     */
    public function hasXpaymentsSubscriptions()
    {
        $result = false;

        foreach ($this->getItems() as $item) {
            if ($item->isXpaymentsSubscription()) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Checks if all items in cart are subscriptions with trial period
     *
     * @return bool
     */
    public function hasOnlyTrialSubscriptionItems()
    {
        if (count($this->getItems())) {

            $result = true;

            foreach ($this->getItems() as $item) {
                if (
                    !$item->isXpaymentsSubscription()
                    || !$item->hasTrialPeriod()
                ) {
                    $result = false;
                    break;
                }
            }

        } else {

            $result = false;
        }

        return $result;
    }

    /**
     * isXpaymentsSubscriptionPayment
     *
     * @return boolean
     */
    public function isXpaymentsSubscriptionPayment()
    {
        $isXpaymentsSubscriptionPayment = false;

        foreach ($this->getItems() as $item) {
            if ($item->getXpaymentsSubscription()
                && !$item->isInitialXpaymentsSubscription()
            ) {
                $isXpaymentsSubscriptionPayment = true;
            }
        }

        return $isXpaymentsSubscriptionPayment;
    }

    /**
     * getXpaymentsSubscription
     *
     * @return Subscription
     */
    public function getXpaymentsSubscription()
    {
        $subscription = null;

        if ($this->isXpaymentsSubscriptionPayment()) {
            foreach ($this->getItems() as $item) {
                if ($item->isXpaymentsSubscription()
                    && !$item->isInitialXpaymentsSubscription()
                ) {
                    $subscription = $item->getXpaymentsSubscription();
                }
            }
        }

        return $subscription;
    }

    /**
     * Get shipping rates for order
     *
     * @return array
     */
    public function getShippingRates()
    {
        $rates = $this->getModifier(\XLite\Model\Base\Surcharge::TYPE_SHIPPING, 'SHIPPING')->getRates();
        $result = array();
        foreach ($rates as $rate) {
            $result[] = array(
                'method_id' => $rate->getMethodId(),
                'method_name' => $rate->getMethodName(),
                'total_rate' => $rate->getTotalRate(),
            );
        }

        return $result;
    }

    /**
     * Get shipping address
     *
     * @return \XLite\Model\Address
     */
    public function getShippingAddress()
    {
        return $this->getProfile()->getShippingAddress();
    }

    // \ X-Payments subscriptions methods

    /**
     * Returns true if order is allowed to change status on succeed
     *
     * @return boolean
     */
    protected function isPaymentMethodRequired()
    {
        return ($this->hasOnlyTrialSubscriptionItems() && self::ORDER_ZERO >= $this->getOpenTotal())
            || parent::isPaymentMethodRequired();
    }

    /**
     * Add payment transaction
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     * @param float                       $value  Value OPTIONAL
     *
     * @return void
     */
    protected function addPaymentTransaction(\XLite\Model\Payment\Method $method, $value = null)
    {
        parent::addPaymentTransaction($method, $value);

        if ($method->isXpayments()) {
            if (null === $value || 0 >= $value) {
                $value = $this->getOpenTotal();
            } else {
                $value = min($value, $this->getOpenTotal());
            }
            // Force add zero transactions for trial subscriptions
            if ($value <= 0) {
                $this->addZeroTotalPaymentTransaction($method);
            }
        }
    }

    /**
     * Force add payment transaction even if its value is zero
     *
     * @param \XLite\Model\Payment\Method $method Payment method
     *
     * @throws \Doctrine\ORM\ORMInvalidArgumentException
     *
     * @return void
     */
    protected function addZeroTotalPaymentTransaction(\XLite\Model\Payment\Method $method)
    {
        $transaction = new \XLite\Model\Payment\Transaction();

        $this->addPaymentTransactions($transaction);
        $transaction->setOrder($this);

        $transaction->setPaymentMethod($method);

        \XLite\Core\Database::getEM()->persist($method);

        $transaction->setCurrency($this->getCurrency());

        $transaction->setStatus($transaction::STATUS_INITIALIZED);
        $transaction->setValue(self::ORDER_ZERO);
        $transaction->setType($method->getProcessor()->getInitialTransactionType($method));

        if ($method->getProcessor()->isTestMode($method)) {
            $transaction->setDataCell(
                'test_mode',
                true,
                'Test mode'
            );
        }

        \XLite\Core\Database::getEM()->persist($transaction);
    }

}
