<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * QbcOrderStatus model
 *
 * @ORM\Entity
 * @ORM\Table  (name="quickbooks_order_statuses",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="qbc_unique_status", columns={"payment_status_id", "shipping_status_id"})
 *      },
 *      indexes={
 *          @ORM\Index (name="payment_status", columns={"payment_status_id"}),
 *          @ORM\Index (name="shipping_status", columns={"shipping_status_id"})
 *      }
 * )
 */
class QbcOrderStatus extends AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={"unsigned": true})
     */
    protected $id;
    
    /**
     * Payment status
     *
     * @var \XLite\Model\Order\Status\Payment
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Payment")
     * @ORM\JoinColumn (name="payment_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $paymentStatus;

    /**
     * Shipping status
     *
     * @var \XLite\Model\Order\Status\Shipping
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order\Status\Shipping")
     * @ORM\JoinColumn (name="shipping_status_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $shippingStatus;
    
    /**
     * Get Id 
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set Id
     * 
     * @param integer $id
     * 
     * @return QbcOrderStatus
     */
    public function setId($id)
    {
        $this->id = $id;
        
        return $this;
    }
    
    /**
     * Set payment status
     *
     * @param mixed $paymentStatus Payment status
     *
     * @return QbcOrderStatus
     */
    public function setPaymentStatus($paymentStatus = null)
    {
        $this->processStatus($paymentStatus, 'payment');
    }

    /**
     * Set shipping status
     *
     * @param mixed $shippingStatus Shipping status
     *
     * @return QbcOrderStatus
     */
    public function setShippingStatus($shippingStatus = null)
    {
        $this->processStatus($shippingStatus, 'shipping');
    }
    
    /**
     * Process order status
     *
     * @param mixed  $status Status
     * @param string $type   Type
     *
     * @return void
     */
    public function processStatus($status, $type)
    {
        if (is_scalar($status)) {
            if (
                is_int($status)
                || (is_string($status)
                    && preg_match('/^[\d]+$/', $status)
                )
            ) {
                $status = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\\' . ucfirst($type))
                    ->find($status);
            } elseif (is_string($status)) {
                $status = \XLite\Core\Database::getRepo('XLite\Model\Order\Status\\' . ucfirst($type))
                    ->findOneByCode($status);
            }
        }

        $this->{$type . 'Status'} = $status;
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
     * Get shippingStatus
     *
     * @return \XLite\Model\Order\Status\Shipping
     */
    public function getShippingStatus()
    {
        return $this->shippingStatus;
    }
}