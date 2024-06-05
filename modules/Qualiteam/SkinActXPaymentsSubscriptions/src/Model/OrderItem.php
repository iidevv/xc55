<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XCart\Extender\Mapping\Extender;
use XLite\Logic\Price;

/**
 * Something customer can put into his cart
 *
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{
    /**
     * Subscription
     *
     * @var Subscription
     *
     * @ORM\OneToOne (targetEntity="Qualiteam\SkinActXPaymentsSubscriptions\Model\Subscription", mappedBy="initial_item_id", cascade={"all"})
     * @ORM\JoinColumn (name="subscription_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $subscription;

    /**
     * Check if order item is subscription
     *
     * @return boolean
     */
    public function isSubscription()
    {
        $isSubscription = false;

        if (!is_null($this->getSubscription())) {
            $isSubscription = true;

        } elseif (!is_null($this->getProduct())) {
            $isSubscription = $this->getProduct()->hasSubscriptionPlan();
        }

        return $isSubscription;
    }

    /**
     * Get net price
     * Override magic method (see $price field annotation)
     *
     * @return float
     */
    public function getNetPrice()
    {
        return ($this->getSubscription() && $this->getSubscription()->getInitialOrderItem() !== $this)
            ? $this->getNetFeePrice()
            : parent::getNetPrice();
    }

    /**
     * isInitialSubscription
     *
     * @return boolean
     */
    public function isInitialSubscription()
    {
        return $this->isSubscription()
            && $this->getSubscription()
            && $this->getItemId() === $this->getSubscription()->getInitialOrderItem()->getItemId();
    }

    /**
     * Get setup fee
     *
     * @return float
     */
    public function getSetupFee()
    {
        return $this->isInitialSubscription()
            ? $this->getDisplayPrice() - $this->getDisplayFeePrice()
            : 0;
    }

    /**
     * Get subscription fee
     *
     * @return float
     */
    public function getSubscriptionFee()
    {
        return ($this->isSubscription() && $this->getSubscription())
            ? $this->getSubscription()->getFee()
            : 0;
    }

    /**
     * Set subscription
     *
     * @param Subscription $subscription
     * @return OrderItem
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

    /**
     * Get clear fee price
     *
     * @return float
     */
    public function getClearFeePrice()
    {
        return $this->getProduct()->getClearFeePrice();
    }

    /**
     * Get net fee Price
     *
     * @return float
     */
    public function getNetFeePrice()
    {
        return Price::getInstance()->apply($this, 'getClearFeePrice', ['taxable'], 'netFee');
    }

    /**
     * Get display fee Price
     *
     * @return float
     */
    public function getDisplayFeePrice()
    {
        return Price::getInstance()->apply($this, 'getNetFeePrice', ['taxable'], 'displayFee');
    }

    /**
     * Is next payment date available for current order
     *
     * @return boolean
     */
    protected function isNextPaymentDateAvailable()
    {
        $subscription = $this->getSubscription();

        return $subscription
            && ASubscriptionPlan::STATUS_ACTIVE == $subscription->getStatus()
            && (
                !$subscription->getLastOrderId()
                || $subscription->getLastOrderId() == $this->getOrder()->getOrderId()
            );
    }
}
