<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Model\Order\Parcel;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class represents a Canada Post parcel items
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_capost_parcel_items")
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
     * Item's parcel (reference to the canada post parcels model)
     *
     * @var \XC\CanadaPost\Model\Order\Parcel
     *
     * @ORM\ManyToOne  (targetEntity="XC\CanadaPost\Model\Order\Parcel", inversedBy="items")
     * @ORM\JoinColumn (name="parcelId", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parcel;

    /**
     * Item's order item (reference to the order items model)
     *
     * @var \XLite\Model\OrderItem
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\OrderItem", inversedBy="capostParcelItems")
     * @ORM\JoinColumn (name="orderItemId", referencedColumnName="item_id",  onDelete="SET NULL")
     */
    protected $orderItem;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $amount = 0;

    // {{{ Service methods

    /**
     * Universal getter
     *
     * @param string $property
     *
     * @return mixed|null Returns NULL if it is impossible to get the property
     */
    public function getterProperty($property)
    {
        $result = null;

        if (property_exists($this, $property)) {
            // Get property value
            $result = $this->$property;
        } elseif ($this->getOrderItem()) {
            $result = $this->getOrderItem()->$property;
        } else {
            $result = parent::getterProperty($property);
        }

        return $result;
    }

    /**
     * Assign the parcel
     *
     * @param \XC\CanadaPost\Model\Order\Parcel $parcel Order's parcel (OPTIONAL)
     *
     * @return void
     */
    public function setParcel(\XC\CanadaPost\Model\Order\Parcel $parcel = null)
    {
        $this->parcel = $parcel;
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
     * Get single item weight (in store weight units)
     *
     * @return float
     */
    public function getSubtotal()
    {
        $object = $this->getOrderItem();

        return $object ? $object->getPrice() * $this->getAmount() : 0;
    }

    /**
     * Get single item weight (in store weight units)
     *
     * @return float
     */
    public function getWeight()
    {
        $result = $this->getObjectWeight();

        foreach ($this->getOrderItem()->getAttributeValues() as $attributeValue) {
            if ($attributeValue->getAttributeValue()) {
                $result += $attributeValue->getAttributeValue()->getAbsoluteValue('weight');
            }
        }

        return $result;
    }

    /**
     * Get object weight (in store weight units)
     *
     * @return float
     */
    protected function getObjectWeight()
    {
        $object = $this->getOrderItem()->getObject();

        return $object
            ? $object->getWeight()
            : 0;
    }

    /**
     * Get single item weight in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getWeightInKg($adjustFloatValue = false)
    {
        // Convert weight from store units to KG (weight must be in KG)
        $weight = \XLite\Core\Converter::convertWeightUnits(
            $this->getWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            'kg'
        );

        if ($adjustFloatValue) {
            // Adjust according to the XML element schema
            $weight = \XC\CanadaPost\Core\Service\AService::adjustFloatValue($weight, 3, 0, 99.999);
        }

        return $weight;
    }

    /**
     * Get total item weight (in store weight units)
     *
     * @return float
     */
    public function getTotalWeight()
    {
        return $this->getWeight() * $this->getAmount();
    }

    /**
     * Get total item weight in KG
     *
     * @param boolean $adjustFloatValue Flag - adjust float value or not (OPTIONAL)
     *
     * @return float
     */
    public function getTotalWeightInKg($adjustFloatValue = false)
    {
        return $this->getWeightInKg($adjustFloatValue) * $this->getAmount();
    }

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
     *
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
     * Get parcel
     *
     * @return \XC\CanadaPost\Model\Order\Parcel
     */
    public function getParcel()
    {
        return $this->parcel;
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
