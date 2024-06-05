<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * Quickbooks orders model
 *
 * @ORM\Entity
 * @ORM\Table  (name="quickbooks_orders",
 *      indexes={
 *          @ORM\Index (name="quickbooks_editsequence", columns={"quickbooks_editsequence"}),
 *          @ORM\Index (name="quickbooks_txnid", columns={"quickbooks_txnid"})
 *      }
 * )
 */
class QuickbooksOrders extends AEntity
{
    /**
     * Order ID
     *
     * @var XLite\Model\Order
     *
     * @ORM\Id
     * @ORM\OneToOne   (targetEntity="XLite\Model\Order", cascade={"merge","detach","persist"})
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order_id;
    
    /**
     * Quickbooks editsequence
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $quickbooks_editsequence;

    /**
     * Quickbooks txnid
     *
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $quickbooks_txnid;
    
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
     * Set order id
     * 
     * @param integer $id
     * 
     * @return QuickbooksOrders
     */
    public function setOrderId($id)
    {
        $this->order_id = $id;
        
        return $this;
    }
    
    /**
     * Set quickbooks editsequence
     *
     * @param string $value
     *
     * @return QuickbooksOrders
     */
    public function setQuickbooksEditsequence($value)
    {
        $this->quickbooks_editsequence = $value;
    }
    
    /**
     * Set quickbooks txnid
     *
     * @param string $value
     *
     * @return QuickbooksOrders
     */
    public function setQuickbooksTxnid($value)
    {
        $this->quickbooks_txnid = $value;
    }

    /**
     * Get Quickbooks Editsequence
     *
     * @return string
     */
    public function getQuickbooksEditsequence()
    {
        return $this->quickbooks_editsequence;
    }

    /**
     * Get Quickbooks txnid
     *
     * @return string
     */
    public function getQuickbooksTxnid()
    {
        return $this->quickbooks_txnid;
    }
}