<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActXPaymentsSubscriptions\Core\Converter;
use Qualiteam\SkinActXPaymentsSubscriptions\Model\Base\ASubscriptionPlan;
use XLite\Core\Database;

/**
 * Subscription Plan
 *
 * @ORM\Entity
 * @ORM\Table  (name="subscription_plan")
 */
class SubscriptionPlan extends ASubscriptionPlan
{
    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Product", inversedBy="subscriptionPlan")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * Is product is subscription plan
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $subscription = false;

    /**
     * Setup fee for plan
     *
     * @var float
     *
     * @ORM\Column (type="money")
     */
    protected $setupFee = 0.0000;

    /**
     * Calculate shipping for recurring orders
     *
     * @var boolean
     * 
     * @ORM\Column (type="boolean")
     */
    protected $calculate_shipping = 0;

    /**
     * Return array of field for plan form filed
     *
     * @return array
     */
    public function getPlan()
    {
        $type = $this->getType();
        $number = $this->getNumber();
        $numberSuffix = static::TYPE_EACH == $type
            ? static::t('xps.number_suffix', ['number' => $number])
            : static::t(' {{tp|период|периода|периодов|number}} в ', ['number' => $number]);

        return [
            'type' => $type,
            'number' => $number,
            'number_suffix' => $numberSuffix,
            'period' => $this->getPeriod(),
            'reverse' => $this->getReverse(),
            'description' => $this->getPlanDescription(),
        ];
    }

    /**
     * Set plan fields
     *
     * @param array $data Plan data
     *
     * @return void
     */
    public function setPlan($data)
    {
        $this->setType($data['type']);
        $this->setNumber($data['number']);
        $this->setPeriod($data['period']);
        $this->setReverse($data['reverse']);
    }

    /**
     * Return subscription based on current plan
     *
     * @return Subscription
     */
    public function createSubscription()
    {
        $subscription = new Subscription();

        $subscription->setType($this->getType());
        $subscription->setNumber($this->getNumber());
        $subscription->setPeriod($this->getPeriod());
        $subscription->setReverse($this->getReverse());
        $subscription->setPeriods($this->getPeriods());
        $subscription->setCalculateShipping($this->getCalculateShipping());

        $subscription->setStartDate(Converter::now());
        $subscription->setPlannedDate(Converter::now());
        $subscription->setRealDate(Converter::now());

        Database::getEM()->persist($subscription);
        Database::getEM()->flush();

        return $subscription;
    }

    /**
     * Set subscription
     *
     * @param boolean $subscription
     * @return SubscriptionPlan
     */
    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
        return $this;
    }

    /**
     * Get subscription
     *
     * @return boolean 
     */
    public function getSubscription()
    {
        return $this->subscription;
    }

    /**
     * Set setupFee
     *
     * @param float $setupFee
     * @return SubscriptionPlan
     */
    public function setSetupFee($setupFee)
    {
        $this->setupFee = $setupFee;
        return $this;
    }

    /**
     * Get setupFee
     *
     * @return float 
     */
    public function getSetupFee()
    {
        return $this->setupFee;
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
     * @return SubscriptionPlan
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
     * @return SubscriptionPlan
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
     * @return SubscriptionPlan
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
     * @return SubscriptionPlan
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
     * @return SubscriptionPlan
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
     * @return SubscriptionPlan
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return SubscriptionPlan
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set value of "Calculate shipping for recurring orders" option of subscription plan
     *
     * @param boolean $calculateShipping
     * @return SubscriptionPlan
     */
    public function setCalculateShipping($calculateShipping)
    {
        $this->calculate_shipping = $calculateShipping;
        return $this;
    }

    /**
     * Get value of "Calculate shipping for recurring orders" option of subscription plan
     *
     * @return string
     */
    public function getCalculateShipping()
    {
        return $this->calculate_shipping;
    }
}
