<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Order return item
 *
 * @ORM\Entity (repositoryClass="\QSL\Returns\Model\Repo\ReturnItem")
 * @ORM\Table (name="order_return_items")
 */
class ReturnItem extends \XLite\Model\AEntity
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = 0;

    /**
     * Order return
     *
     * @var \QSL\Returns\Model\OrderReturn
     *
     * @ORM\ManyToOne  (targetEntity="\QSL\Returns\Model\OrderReturn", inversedBy="items")
     * @ORM\JoinColumn (name="orderReturnId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $orderReturn;

    /**
     * Order item
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\ManyToOne  (targetEntity="\XLite\Model\OrderItem", inversedBy="returnItems", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="orderItemId", referencedColumnName="item_id", onDelete="SET NULL")
     */
    protected $orderItem;

    /**
     * Item name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    // {{{ Service methods

    /**
     * @var \XLite\Model\OrderItem
     */
    protected $displayOrderItem;

    /**
     * Assign the return
     *
     * @param \QSL\Returns\Model\OrderReturn $return Products return (OPTIONAL)
     *
     * @return void
     */
    public function setReturn(\QSL\Returns\Model\OrderReturn $return = null)
    {
        $this->orderReturn = $return;
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
        $this->displayOrderItem = $orderItem;
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
     * @return ReturnItem
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return ReturnItem
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Set orderReturn
     *
     * @param \QSL\Returns\Model\OrderReturn $orderReturn
     * @return ReturnItem
     */
    public function setOrderReturn(\QSL\Returns\Model\OrderReturn $orderReturn = null)
    {
        $this->orderReturn = $orderReturn;
        return $this;
    }

    /**
     * Get orderReturn
     *
     * @return \QSL\Returns\Model\OrderReturn
     */
    public function getOrderReturn()
    {
        return $this->orderReturn;
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

    /**
     * @return \XLite\Model\OrderItem
     */
    public function getDisplayOrderItem()
    {
        if ($this->orderItem !== null) {
            return $this->orderItem;
        }

        if ($this->displayOrderItem === null) {
            $this->displayOrderItem = new \XLite\Model\OrderItem();
            $this->displayOrderItem->setOrder($this->getOrderReturn()->getOrder());
            $this->displayOrderItem->setAmount($this->getAmount());
            $this->displayOrderItem->setName($this->getName());
            $this->displayOrderItem->detach();
        }

        return $this->displayOrderItem;
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
}
