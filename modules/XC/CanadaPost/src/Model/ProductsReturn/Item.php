<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\ProductsReturn;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents an return items model
 *
 * @ORM\Entity
 * @ORM\Table  (name="capost_return_items")
 */
class Item extends \XLite\Model\AEntity
{
    /**
     * Item unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Reference to the return model
     *
     * @var \XC\CanadaPost\Model\ProductsReturn
     *
     * @ORM\ManyToOne  (targetEntity="XC\CanadaPost\Model\ProductsReturn", inversedBy="items")
     * @ORM\JoinColumn (name="returnId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $return;

    /**
     * Reference to the order item model
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="capostReturnItems")
     * @ORM\JoinColumn (name="orderItemId", referencedColumnName="item_id", onDelete="CASCADE")
     */
    protected $orderItem;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = 0;

    // {{{ Service methods

    /**
     * Assign the return
     *
     * @param \XC\CanadaPost\Model\ProductsReturn $return Products return (OPTIONAL)
     *
     * @return void
     */
    public function setReturn(\XC\CanadaPost\Model\ProductsReturn $return = null)
    {
        $this->return = $return;
    }

    /**
     * Assign the order item
     *
     * @param \XLite\Model\OrderItem $orderItem Order's item (OPTIONAL)
     *
     * @return void
     */
    public function setOrderItem(\XLite\Model\OrderItem $orderItem = null)
    {
        $this->orderItem = $orderItem;
    }

    // }}}

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
     * Set amount
     *
     * @param integer $amount
     * @return Item
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get return
     *
     * @return \XC\CanadaPost\Model\ProductsReturn
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * Get orderItem
     *
     * @return \XLite\Model\OrderItem
     */
    public function getOrderItem()
    {
        return $this->orderItem;
    }
}
