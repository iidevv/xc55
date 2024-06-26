<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * Product
 *
 * @Extender\Mixin
 */
abstract class Product extends \XLite\Model\Product implements \XLite\Base\IDecorator
{
    /**
     * Subscription plan
     *
     * @var \XPay\XPaymentsCloud\Model\Subscription\Plan
     *
     * @ORM\OneToOne (targetEntity="XPay\XPaymentsCloud\Model\Subscription\Plan", mappedBy="product", cascade={"all"})
     */
    protected $xpaymentsSubscriptionPlan;

    /**
     * Check if product has subscription plan
     *
     * @return boolean
     */
    public function hasXpaymentsSubscriptionPlan()
    {
        return null !== $this->getXpaymentsSubscriptionPlan() && $this->getXpaymentsSubscriptionPlan()->getIsSubscription();
    }

    /**
     * Get price: modules should never overwrite this method
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->hasXpaymentsSubscriptionPlan()
            ? ($this->getXpaymentsSubscriptionPlan()->getSetupFee() + $this->getXpaymentsClearFeePrice())
            : parent::getPrice();
    }

    /**
     * Set price
     *
     * @param float $price Price
     *
     * @return \XLite\Model\Product
     */
    public function setPrice($price)
    {
        parent::setPrice($price);

        if ($this->hasXpaymentsSubscriptionPlan()) {
            $this->getXpaymentsSubscriptionPlan()->setSetupFee($price);
        }

        return $this;
    }

    /**
     * Set subscriptionPlan
     *
     * @param \XPay\XPaymentsCloud\Model\Subscription\Plan $xpaymentsSubscriptionPlan
     *
     * @return Product
     */
    public function setXpaymentsSubscriptionPlan(\XPay\XPaymentsCloud\Model\Subscription\Plan $xpaymentsSubscriptionPlan = null)
    {
        $this->xpaymentsSubscriptionPlan = $xpaymentsSubscriptionPlan;

        return $this;
    }

    /**
     * Get subscriptionPlan
     *
     * @return \XPay\XPaymentsCloud\Model\Subscription\Plan
     */
    public function getXpaymentsSubscriptionPlan()
    {
        return $this->xpaymentsSubscriptionPlan;
    }

    /**
     * Get clear fee price
     *
     * @return float
     */
    public function getXpaymentsClearFeePrice()
    {
        return $this->hasXpaymentsSubscriptionPlan()
            ? $this->getXpaymentsSubscriptionPlan()->getFee()
            : 0;
    }

    /**
     * Get net fee Price
     *
     * @return float
     */
    public function getXpaymentsNetFeePrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getXpaymentsClearFeePrice', array('taxable'), 'xpaymentsNetFee');
    }

    /**
     * Get display fee Price
     *
     * @return float
     */
    public function getXpaymentsDisplayFeePrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getXpaymentsNetFeePrice', array('taxable'), 'xpaymentsDisplayFee');
    }

    /**
     * @return bool
     */
    public function hasTrialPeriod()
    {
        return $this->hasXpaymentsSubscriptionPlan()
            && $this->getXpaymentsSubscriptionPlan()->hasTrialPeriod();
    }
}
