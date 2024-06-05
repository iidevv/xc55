<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * X-Payments Bulk Operation entity
 *
 * @ORM\Entity
 * @ORM\Table  (name="xpayments_bulk_operations")
 */
class BulkOperation extends \XLite\Model\AEntity
{
    /**
     * Operations
     */
    const OPERATION_CAPTURE = 'capture';
    const OPERATION_REFUND  = 'refund';
    const OPERATION_VOID    = 'void';

    /**
     * Statuses
     */
    const STATUS_PENDING     = 'pending';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_FINISHED    = 'finished';

    /**
     * Order id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column (type="integer", options={ "comment": "Order id" })
     */
    protected $order_id;

    /**
     * X-Payments ID (xpid)
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column (type="string", length=32, options={ "comment": "XPID" })
     */
    protected $xpaymentsId = '';

    /**
     * Bulk operation (capture, void, refund, etc) 
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column (type="string", length=20, options={ "comment": "Order operation code" } )
     */
    protected $operation = self::OPERATION_CAPTURE;

    /**
     * Status of the operation 
     *
     * @var string
     *
     * @ORM\Column (type="string", length=20, options={ "comment": "Order operation status" })
     */
    protected $status = self::STATUS_PENDING;

    /**
     * Batch ID
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, options={ "comment": "Batch operation ID" })
     */
    protected $batchId = '';

    /**
     * Set order id
     *
     * @param string $order_id
     *
     * @return \XPay\XPaymentsCloud\Model\BulkOperation
     */
    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;

        return $this;
    }

    /**
     * Get order id
     *
     * @return integer
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * Set X-Payments ID
     *
     * @param string $xpaymentsId
     *
     * @return \XPay\XPaymentsCloud\Model\BulkOperation
     */
    public function setXpaymentsId($xpaymentsId)
    {
        $this->xpaymentsId = $xpaymentsId;

        return $this;
    }

    /**
     * Get X-Payments ID
     *
     * @return string
     */
    public function getXpaymentsId()
    {
        return $this->xpaymentsId;
    }

    /**
     * Set Batch ID
     *
     * @param string $batchId
     *
     * @return \XPay\XPaymentsCloud\Model\BulkOperation
     */
    public function setBatchId($batchId)
    {
        $this->batchId = $batchId;

        return $this;
    }

    /**
     * Get Batch ID
     *
     * @return string
     */
    public function getBatchId()
    {
        return $this->batchId;
    }

    /**
     * Set operation
     *
     * @param string $operation
     *
     * @return \XPay\XPaymentsCloud\Model\BulkOperation
     */
    public function setOperation($operation)
    {
        $this->operation = $operation;

        return $this;
    }

    /**
     * Get operation
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return \XPay\XPaymentsCloud\Model\BulkOperation
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
}
