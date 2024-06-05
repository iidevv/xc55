<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Payment;

use Doctrine\ORM\Mapping as ORM;

/**
 * Something customer can put into his cart
 *
 * @ORM\Entity
 * @ORM\Table (name="payment_method_settings",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="mn", columns={"method_id","name"})
 *      }
 * )
 */
class MethodSetting extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $setting_id;

    /**
     * Setting name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128)
     */
    protected $name;

    /**
     * Value
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $value = '';

    /**
     * Payment method
     *
     * @var \XLite\Model\Payment\Method
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Payment\Method", inversedBy="settings")
     * @ORM\JoinColumn (name="method_id", referencedColumnName="method_id", onDelete="CASCADE")
     */
    protected $payment_method;

    /**
     * Get setting_id
     *
     * @return integer
     */
    public function getSettingId()
    {
        return $this->setting_id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return MethodSetting
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param string $value
     * @return MethodSetting
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set payment_method
     *
     * @param \XLite\Model\Payment\Method $paymentMethod
     * @return MethodSetting
     */
    public function setPaymentMethod(\XLite\Model\Payment\Method $paymentMethod = null)
    {
        $this->payment_method = $paymentMethod;
        return $this;
    }

    /**
     * Get payment_method
     *
     * @return \XLite\Model\Payment\Method
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }
}
