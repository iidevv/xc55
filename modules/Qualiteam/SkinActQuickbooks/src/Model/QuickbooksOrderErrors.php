<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActQuickbooks\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Model\AEntity;

/**
 * Quickbooks order errors model
 *
 * @ORM\Entity
 * @ORM\Table  (name="quickbooks_order_errors")
 */
class QuickbooksOrderErrors extends AEntity
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
     * Errors
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $errors = '';
    
    /**
     * Send flag (for "Send emails about orders import errors" setting)
     * 
     * @var integer
     *
     * @ORM\Column (type="integer", options={"default": 0})
     */
    protected $send = 0;
    
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
     * Set errors
     *
     * @param string $value
     *
     * @return QuickbooksOrders
     */
    public function setErrors($value)
    {
        $this->errors = $value;
        
        return $this;
    }
    
    /**
     * Get errors
     *
     * @return string
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Set send
     *
     * @param integer $send
     * 
     * @return Order
     */
    public function setSend($send)
    {
        $this->send = $send;
        
        return $this;
    }

    /**
     * Get send
     *
     * @return integer
     */
    public function getSend()
    {
        return $this->send;
    }
}