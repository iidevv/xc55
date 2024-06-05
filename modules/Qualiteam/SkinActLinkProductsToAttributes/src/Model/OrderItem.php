<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActLinkProductsToAttributes\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * Decorate OrderItem model
 * @Extender\Mixin
 */
class OrderItem extends \XLite\Model\OrderItem
{

    /**
     * Relation to a Attribute Value entity
     *
     * @var \XLite\Model\OrderItem\AttributeValue
     *
     * @ORM\OneToOne   (targetEntity="XLite\Model\OrderItem\AttributeValue", inversedBy="linkedOrderItem", cascade={"persist"})
     * @ORM\JoinColumn (name="linked_attribute_value_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $linkedAttributeValue;

    /**
     * Parent order item for linked products
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\ManyToOne (targetEntity="XLite\Model\OrderItem", inversedBy="linked_items")
     * @ORM\JoinColumn (name="parent_item_id", referencedColumnName="item_id", onDelete="SET NULL")
     */
    protected $parent_item;

    /**
     * Child categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\OrderItem", mappedBy="parent_item", cascade={"all"})
     */
    protected $linked_items;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->linked_items         = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    public function getLinkedAttributeValue()
    {
        return $this->linkedAttributeValue;
    }

    public function setLinkedAttributeValue($linkedAttributeValue)
    {
        $this->linkedAttributeValue = $linkedAttributeValue;
    }

    public function getParentItem()
    {
        return $this->parent_item;
    }

    public function setParentItem($parent_item)
    {
        $this->parent_item = $parent_item;
    }

    public function getLinkedItems()
    {
        return $this->linked_items;
    }

    public function addLinkedItems($linked_item)
    {
        $this->linked_items[] = $linked_item;

        return $this;
    }


    /**
     * This key is used when checking if item is unique in the cart
     *
     * @return string
     */
    public function getKey()
    {
        $result = parent::getKey();

        if ($this->isAttributeValueLinked() && !\XLite::isAdminZone() ) {
            $result .= '||linked::' . $this->getLinkedAttributeValue()->getId();
        }

        return $result;
    }

    public function isItemRemovableFromCart()
    {
        return !(bool)$this->getLinkedAttributeValue();
    }

    public function hasLinkedOrderItems()
    {
        return count($this->getLinkedItems()) > 0;
    }

    public function isAttributeValueLinked(): bool
    {
        return $this->getParentItem() && $this->getLinkedAttributeValue();
    }

    /**
     * Get available amount for the product
     *
     * @return integer
     */
    public function getProductAvailableAmount()
    {

        $max_amount = parent::getProductAvailableAmount();

        if ($this->getParentItem()) {
            $configMaxLimit = Config::getInstance()->Qualiteam->SkinActLinkProductsToAttributes->linkedProductMaxQty;
            if ($configMaxLimit > 0) {
                $max_amount = min($configMaxLimit * $this->getParentItem()->getAmount(), $max_amount);
            }
        }
        return $max_amount;
    }


    /**
     * Get item clear price. This value is used as a base item price for calculation of netPrice
     *
     * @return float
     */
    public function getNetPrice()
    {
        if ($this->getLinkedAttributeValue()) {

            return $this->getLinkedAttributeValue()->getAttributeValue()
                ? $this->getLinkedAttributeValue()->getAttributeValue()->getAbsoluteValue('price')
                : parent::getNetPrice();

        }

        if ($this->getLinkedItems()) {

            $return = parent::getNetPrice();
            foreach ($this->getLinkedItems() as $linkedItem) {
               $return -= $linkedItem->getNetPrice();
            }

            return $return;
        }

        return parent::getNetPrice();
    }



}