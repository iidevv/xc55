<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Logic\Price;

/**
 * Product
 *
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Subscription plan
     *
     * @var SubscriptionPlan
     *
     * @ORM\OneToOne (targetEntity="Qualiteam\SkinActXPaymentsSubscriptions\Model\SubscriptionPlan", mappedBy="product", cascade={"all"})
     */
    protected $subscriptionPlan;

    /**
     * Subscription products are not allowed for anonymous
     *
     * @return boolean
     */
    public function isNotAllowedSubscription()
    {
        return (
            !Auth::getInstance()->isLogged()
            && $this->hasSubscriptionPlan()
        );
    }

    /**
     * Return true if product can be purchased in customer interface
     *
     * @return boolean
     */
    public function isPublicAvailable()
    {
        return !$this->isNotAllowedSubscription() && parent::isPublicAvailable();
    }

    /**
     * Check if product has subscription plan
     *
     * @return boolean
     */
    public function hasSubscriptionPlan()
    {
        return null !== $this->getSubscriptionPlan() && $this->getSubscriptionPlan()->getSubscription();
    }

    /**
     * Get price: modules should never overwrite this method
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->hasSubscriptionPlan()
            ? $this->getSubscriptionPlan()->getSetupFee()
            : parent::getPrice();
    }

    /**
     * Set price
     *
     * @param float $price Price
     *
     * @return void|\XLite\Model\Product
     */
    public function setPrice($price)
    {
        parent::setPrice($price);

        if ($this->hasSubscriptionPlan()) {
            $this->getSubscriptionPlan()->setSetupFee($price);
        }

        return $this;
    }

    /**
     * Set subscriptionPlan
     *
     * @param SubscriptionPlan $subscriptionPlan
     * @return Product
     */
    public function setSubscriptionPlan(SubscriptionPlan $subscriptionPlan = null)
    {
        $this->subscriptionPlan = $subscriptionPlan;
        return $this;
    }

    /**
     * Get subscriptionPlan
     *
     * @return SubscriptionPlan
     */
    public function getSubscriptionPlan()
    {
        return $this->subscriptionPlan;
    }

    /**
     * Get clear fee price
     *
     * @return float
     */
    public function getClearFeePrice()
    {
        return $this->hasSubscriptionPlan()
            ? $this->getSubscriptionPlan()->getFee()
            : 0;
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
}
