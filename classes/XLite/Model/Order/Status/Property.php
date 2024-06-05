<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model\Order\Status;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order status config
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_status_properties",
 *      indexes={
 *          @ORM\Index (name="payment_status", columns={"payment_status_id"}),
 *          @ORM\Index (name="shipping_status", columns={"shipping_status_id"})
 *      }
 * )
 */
class Property extends \XLite\Model\AEntity
{
    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Payment status
     *
     * @var \XLite\Model\Order\Status\Payment
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Payment", cascade={"all"})
     * @ORM\JoinColumn (name="payment_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $paymentStatus;

    /**
     * Shipping status
     *
     * @var \XLite\Model\Order\Status\Shipping
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Shipping", cascade={"all"})
     * @ORM\JoinColumn (name="shipping_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $shippingStatus;

    /**
     * Increase (true) or decrease (false) inventory
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $incStock;

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
     * Set incStock
     *
     * @param boolean $incStock
     * @return Property
     */
    public function setIncStock($incStock)
    {
        $this->incStock = $incStock;
        return $this;
    }

    /**
     * Get incStock
     *
     * @return boolean
     */
    public function getIncStock()
    {
        return $this->incStock;
    }

    /**
     * Set paymentStatus
     *
     * @param \XLite\Model\Order\Status\Payment $paymentStatus
     * @return Property
     */
    public function setPaymentStatus(\XLite\Model\Order\Status\Payment $paymentStatus = null)
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    /**
     * Get paymentStatus
     *
     * @return \XLite\Model\Order\Status\Payment
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Set shippingStatus
     *
     * @param \XLite\Model\Order\Status\Shipping $shippingStatus
     * @return Property
     */
    public function setShippingStatus(\XLite\Model\Order\Status\Shipping $shippingStatus = null)
    {
        $this->shippingStatus = $shippingStatus;
        return $this;
    }

    /**
     * Get shippingStatus
     *
     * @return \XLite\Model\Order\Status\Shipping
     */
    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }
}
