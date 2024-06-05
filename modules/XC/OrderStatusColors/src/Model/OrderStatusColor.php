<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;
use XLite\Model\Order\Status\Payment;
use XLite\Model\Order\Status\Shipping;

/**
 * order status color
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_status_color",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"payment_status_id","shipping_status_id"})
 *      }
 * )
 *
 */
class OrderStatusColor extends AEntity
{
    /**
     * Primary key
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
     * @var Payment
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Payment")
     * @ORM\JoinColumn (name="payment_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $paymentStatus;

    /**
     * Shipping status
     *
     * @var Shipping
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Shipping")
     * @ORM\JoinColumn (name="shipping_status_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $shippingStatus;

    /**
     * Color
     *
     * @var string
     *
     * @ORM\Column (type="string", length=6, nullable=true)
     */
    protected $color = '';

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
     * Return payment status
     *
     * @return Payment
     */
    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    /**
     * Return shipping status
     *
     * @return Shipping
     */
    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }

    /**
     * Return color hex code
     *
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set payment status
     *
     * @param Payment $paymentStatus
     */
    public function setPaymentStatus(Payment $paymentStatus)
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * Set shipping status
     *
     * @param Shipping $shippingStatus
     */
    public function setShippingStatus(Shipping $shippingStatus)
    {
        $this->shippingStatus = $shippingStatus;
    }

    /**
     * Set color hex code
     *
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }
}
