<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model\Subscription;

use Doctrine\ORM\Mapping as ORM;

/**
 * X-Payments Subscription Plan entity
 *
 * @ORM\Entity
 * @ORM\Table  (name="xpayments_subscription_plans")
 */
class Plan extends Base\ASubscriptionPlan
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
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\Product", inversedBy="xpaymentsSubscriptionPlan")
     * @ORM\JoinColumn (name="productId", referencedColumnName="product_id")
     */
    protected $product;

    /**
     * Is the product a subscription plan
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={ "comment": "Is the product a subscription plan" })
     */
    protected $isSubscription = false;

    /**
     * Setup fee for plan
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4, options={ "comment": "Setup fee for the plan" })
     */
    protected $setupFee = 0.0000;

    /**
     * Whether to calculate shipping for recurring orders
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={ "comment": "Whether to calculate shipping for recurring orders" })
     */
    protected $calculateShipping = false;

    /**
     * Trial duration (measured in the respective unit)
     *
     * @var int
     *
     * @ORM\Column (type="integer", options={ "default": 0, "comment": "Trial duration (in days / weeks / months / years)" })
     */
    protected $trialDuration = 0;

    /**
     * Trial duration unit (days / weeks / months / years)
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true, "default": "D" }, length=1)
     */
    protected $trialDurationUnit = self::TRIAL_DURATION_UNIT_DAY;

    /**
     * Has trial period or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", options={ "default": false, "comment": "Has trial period or not" })
     */
    protected $hasTrialPeriod = false;

    /**
     * Set subscription
     *
     * @param boolean $isSubscription
     *
     * @return Plan
     */
    public function setIsSubscription($isSubscription)
    {
        $this->isSubscription = $isSubscription;
        return $this;
    }

    /**
     * Is subscription
     *
     * @return boolean
     */
    public function getIsSubscription()
    {
        return $this->isSubscription;
    }

    /**
     * Set setupFee
     *
     * @param float $setupFee
     *
     * @return Plan
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
     *
     * @return Plan
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
     *
     * @return Plan
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
     *
     * @return Plan
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
     *
     * @return Plan
     */
    public function setReverse($reverse)
    {
        $this->reverse = (bool)$reverse;
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
     *
     * @return Plan
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
     * Set product
     *
     * @param \XLite\Model\Product $product
     *
     * @return Plan
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
     *
     * @return Plan
     */
    public function setCalculateShipping($calculateShipping)
    {
        $this->calculateShipping = $calculateShipping;
        return $this;
    }

    /**
     * Get value of "Calculate shipping for recurring orders" option of subscription plan
     *
     * @return string
     */
    public function getCalculateShipping()
    {
        return $this->calculateShipping;
    }

    /**
     * Getter for hasTrialPeriod (used by core automatically)
     *
     * @return bool
     */
    public function getHasTrialPeriod()
    {
        return $this->hasTrialPeriod;
    }

    /**
     * Pretty named check for trial period
     *
     * @return bool
     */
    public function hasTrialPeriod()
    {
        return $this->getHasTrialPeriod();
    }

    /**
     * @param bool $hasTrialPeriod
     *
     * @return Plan
     */
    public function setHasTrialPeriod($hasTrialPeriod)
    {
        $this->hasTrialPeriod = $hasTrialPeriod;
        return $this;
    }

    /**
     * @return int
     */
    public function getTrialDuration()
    {
        return $this->trialDuration;
    }

    /**
     * @param int $trialDuration
     *
     * @return Plan
     */
    public function setTrialDuration($trialDuration)
    {
        $this->trialDuration = $trialDuration;
        return $this;
    }

    /**
     * @return string
     */
    public function getTrialDurationUnit()
    {
        return $this->trialDurationUnit;
    }

    /**
     * @param int $trialDurationUnit
     *
     * @return Plan
     */
    public function setTrialDurationUnit($trialDurationUnit)
    {
        $this->trialDurationUnit = $trialDurationUnit;
        return $this;
    }

}
