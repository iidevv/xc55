<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;
use XLite\Core\Request;
use XLite\Model\OrderItem\Surcharge;

/**
 * Something customer can put into his cart
 *
 * @ORM\Entity
 * @ORM\Table  (name="order_items",
 *          indexes={
 *               @ORM\Index (name="ooo", columns={"order_id","object_type","object_id"}),
 *               @ORM\Index (name="object_id", columns={"object_id"}),
 *               @ORM\Index (name="price", columns={"price"}),
 *               @ORM\Index (name="amount", columns={"amount"})
 *          }
 * )
 *
 * @ORM\InheritanceType       ("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn   (name="object_type", type="string", length=16)
 * @ORM\DiscriminatorMap      ({"product" = "XLite\Model\OrderItem"})
 */
class OrderItem extends \XLite\Model\Base\SurchargeOwner
{
    public const PRODUCT_TYPE = 'product';

    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $item_id;

    /**
     * Object (product)
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="order_items", cascade={"merge","detach"})
     * @ORM\JoinColumn (name="object_id", referencedColumnName="product_id", onDelete="SET NULL")
     */
    protected $object;

    /**
     * Item name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * Item SKU
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32)
     */
    protected $sku = '';

    /**
     * Item price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price;

    /**
     * Item net price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $itemNetPrice;

    /**
     * Item discounted subtotal
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $discountedSubtotal = 0;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $amount = 1;

    /**
     * Item quantity
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $backorderedAmount = 0;

    /**
     * Item order
     *
     * @var \XLite\Model\Order
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Order", inversedBy="items")
     * @ORM\JoinColumn (name="order_id", referencedColumnName="order_id", onDelete="CASCADE")
     */
    protected $order;

    /**
     * Order item surcharges
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\OrderItem\Surcharge", mappedBy="owner", cascade={"all"})
     * @ORM\OrderBy   ({"weight" = "ASC", "id" = "ASC"})
     */
    protected $surcharges;

    /**
     * Dump product (deleted)
     *
     * @var \XLite\Model\Product
     */
    protected $dumpProduct;

    /**
     * Attribute values
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\OrderItem\AttributeValue", mappedBy="orderItem", cascade={"all"})
     */
    protected $attributeValues;

    /**
     * Update date (UNIX timestamp)
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $updateDate = 0;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->surcharges = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValues = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getObject() ? $this->getObject()->getName() : $this->name;
    }

    /**
     * Set order
     *
     * @param \XLite\Model\Order $order Order OPTIONAL
     */
    public function setOrder(\XLite\Model\Order $order = null)
    {
        $this->order = $order;
    }

    /**
     * Clone order item object. The product only is set additionally
     * since the order could be different and should be set manually
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newItem = parent::cloneEntity();

        if ($this->getObject()) {
            $newItem->setObject($this->getObject());
        }

        foreach ($this->getSurcharges() as $surchrg) {
            $cloned = $surchrg->cloneEntity();
            $cloned->setOwner($newItem);
            $newItem->addSurcharges($cloned);
        }

        if ($this->hasAttributeValues()) {
            foreach ($this->getAttributeValues() as $av) {
                $cloned = $av->cloneEntity();
                $cloned->setOrderItem($newItem);
                $newItem->addAttributeValues($cloned);
            }
        }

        return $newItem;
    }

    /**
     * Get item clear price. This value is used as a base item price for calculation of netPrice
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getProduct()->getClearPrice();
    }

    /**
     * Get net Price
     *
     * @return float
     */
    public function getNetPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getClearPrice', ['taxable'], 'net');
    }

    /**
     * Get display Price
     *
     * @return float
     */
    public function getDisplayPrice()
    {
        return \XLite\Logic\Price::getInstance()->apply($this, 'getNetPrice', ['taxable'], 'display');
    }

    /**
     * Get item price
     *
     * @return float
     */
    public function getItemPrice()
    {
        return $this->isOrderOpen() ? $this->getClearPrice() : $this->getPrice();
    }

    /**
     * Get item net price
     *
     * @return float
     */
    public function getItemNetPrice()
    {
        return $this->isOrderOpen() ? $this->getNetPrice() : $this->itemNetPrice;
    }

    /**
     * Return false if order is fixed in the database (i.e. order is placed) and true if order is still used as "cart"
     *
     * @return boolean
     */
    public function isOrderOpen()
    {
        $order = $this->getOrder();

        return $order && method_exists($order, 'hasCartStatus') && $order->hasCartStatus();
    }

    /**
     * Get through exclude surcharges
     *
     * @return array
     */
    public function getThroughExcludeSurcharges()
    {
        $list = $this->getOrder()->getItemsExcludeSurcharges();

        foreach ($list as $key => $value) {
            $list[$key] = null;

            foreach ($this->getExcludeSurcharges() as $surcharge) {
                if ($surcharge->getKey() == $key) {
                    $list[$key] = $surcharge;
                    break;
                }
            }
        }

        return $list;
    }

    /**
     * Wrapper. If the product was deleted,
     * item will use save product name and SKU
     * TODO - switch to getObject() and remove
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        if ($this->isDeleted()) {
            $result = $this->getDeletedProduct();
        } else {
            $result = $this->getObject();
            $result->setAttrValues($this->getAttributeValuesIds());
        }

        return $result;
    }

    /**
     * Get item product id if it exists
     *
     * @return int|null
     */
    public function getProductId()
    {
        return $this->isDeleted() ? null : $this->getObject()->getProductId();
    }

    /**
     * Save some fields from product
     *
     * @param \XLite\Model\Product $product Product to set OPTIONAL
     *
     * @return void
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->setObject($product);
    }

    /**
     * Set object
     *
     * @param \XLite\Model\Base\IOrderItem $item Order item related object OPTIONAL
     *
     * @return void
     */
    public function setObject(\XLite\Model\Base\IOrderItem $item = null)
    {
        $this->object = $item;

        if ($item) {
            $this->saveItemState($item);
        } else {
            $this->resetItemState();
        }
    }

    /**
     * Define the warning if amount is less or more than purchase limits
     *
     * @param integer $amount
     *
     * @return string
     */
    public function getAmountWarning($amount)
    {
        $result = '';
        if ($this->getObject() === null) {
            return $result;
        }

        $minQuantity = $this->getObject()->getMinPurchaseLimit();
        $maxQuantity = $this->getObject()->getMaxPurchaseLimit();

        if ($amount < $minQuantity) {
            //There's a minimum purchase limit of MinQuantity. The number of units of the product ProductName in cart has been adjusted to reach this limit.
            $result = \XLite\Core\Translation::lbl('There is a minimum purchase limit of MinQuantity', [
                'minQuantity' => $minQuantity,
                'productName' => $this->getName(),
            ]);
        } elseif ($amount > $maxQuantity) {
            $result = \XLite\Core\Translation::lbl('There is a maximum purchase limit of MaxQuantity', [
                'maxQuantity' => $maxQuantity,
                'productName' => $this->getName(),
            ]);
        }

        return $result;
    }

    /**
     * Modified setter
     *
     * @param integer $amount Value to set
     *
     * @return void
     */
    public function setAmount($amount)
    {
        $correctedAmount = $amount;
        if (
            ($amount !== $this->getAmount() && !\XLite::isAdminZone() && !Request::getInstance()->isCLI())
            || !$this->getOrder()
            || $this->isOrderOpen()
        ) {
            $correctedAmount = $this->processAmount($amount);

            if ($warningText = $this->getAmountWarning($amount)) {
                \XLite\Core\TopMessage::addWarning($warningText);
            }
        }

        $this->amount = $correctedAmount;
    }

    /**
     * Set Backordered Amount
     *
     * @param int $backorderedAmount
     *
     * @return $this
     */
    public function setBackorderedAmount($backorderedAmount)
    {
        $this->backorderedAmount = $backorderedAmount;
        return $this;
    }

    /**
     * Process amount value before set
     *
     * @param $amount
     *
     * @return mixed
     */
    public function processAmount($amount)
    {
        if ($this->getObject()) {
            $amount = max($amount, $this->getObject()->getMinPurchaseLimit());
            $amount = min($amount, $this->getObject()->getMaxPurchaseLimit());
        }

        return $amount;
    }

    /**
     * Get item weight
     *
     * @return float
     */
    public function getWeight()
    {
        $result = $this->getClearWeight();

        foreach ($this->getAttributeValues() as $attributeValue) {
            $av = $attributeValue->getAttributeValue();
            if (is_object($av)) {
                $result += $av->getAbsoluteValue('weight');
            }
        }

        return 0 < $result
            ? $result * $this->getAmount()
            : 0;
    }

    /**
     * Get clear weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getObject() ? $this->getObject()->getClearWeight() : 0;
    }

    /**
     * Check if item has a image
     *
     * @return boolean
     */
    public function hasImage()
    {
        return $this->getImage() !== null && (bool) $this->getImage()->getId();
    }

    /**
     * Check if item has a wrong amount
     *
     * @return boolean
     */
    public function hasWrongAmount()
    {
        return $this->getProduct()->getInventoryEnabled()
            && ($this->getProduct()->getPublicAmount() < $this->getAmount());
    }

    /**
     * Get item image URL
     *
     * @return string
     */
    public function getImageURL()
    {
        return $this->getImage()->getURL();
    }

    /**
     * Get item image relative URL
     *
     * @return string
     */
    public function getImageRelativeURL()
    {
        return \Includes\Utils\FileManager::getRelativePath($this->getImage()->getStoragePath(), LC_DIR_ROOT);
    }
    /**
     * Get item resized image relative URL
     *
     * @return string
     */
    public function getResizedImageURL($width, $height)
    {
        $img = $this->getImage();
        $img->doResize($width, $height);

        return $img->getResizedURL($width, $height)[2];
    }

    /**
     * Get item image
     *
     * @return \XLite\Model\Base\Image
     */
    public function getImage()
    {
        return $this->getProduct()->getImage();
    }

    /**
     * Get minicart image width
     *
     * @return string
     */
    public function getMiniCartImageWidth()
    {
        return 60;
    }

    /**
     * Get minicart image height
     *
     * @return string
     */
    public function getMiniCartImageHeight()
    {
        return 60;
    }

    /**
     * Get item description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->getProduct()->getName() . ' (' . $this->getAmount() . ')';
    }

    /**
     * Get extended item description
     *
     * @return string
     */
    public function getExtendedDescription()
    {
        return '';
    }

    /**
     * Get available amount for the product
     *
     * @return integer
     */
    public function getProductAvailableAmount()
    {

        return $this->getProduct()->getInventoryEnabled()
            ? $this->getProduct()->getPublicAmount()
            : $this->getProduct()->getMaxPurchaseLimit();
    }

    /**
     * Get item URL
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getProduct()->getURL();
    }

    /**
     * Flag; is this item needs to be shipped
     *
     * @return boolean
     */
    public function isShippable()
    {
        return !$this->getProduct()->getFreeShipping();
    }

    /**
     * This key is used when checking if item is unique in the cart
     *
     * @return string
     */
    public function getKey()
    {
        $result = static::PRODUCT_TYPE . '.' . ($this->getObject() ? $this->getObject()->getId() : null);
        foreach ($this->getAttributeValues() as $attributeValue) {
            $result .= '||'
                . $attributeValue->getActualName()
                . '::'
                . $attributeValue->getActualValue();
        }

        return $result;
    }

    /**
     * Return attribute values ids
     *
     * @return array
     */
    public function getAttributeValuesIds()
    {
        $result = [];

        foreach ($this->getAttributeValues() as $itemValue) {
            $attributeValue = $itemValue->getAttributeValue();
            if ($attributeValue) {
                if ($attributeValue instanceof \XLite\Model\AttributeValue\AttributeValueText) {
                    $result[$attributeValue->getAttribute()->getId()] = $itemValue->getValue();
                } else {
                    $result[$attributeValue->getAttribute()->getId()] = $attributeValue->getId();
                }
            }
        }
        ksort($result);

        return $result;
    }

    /**
     * Get attribute values as plain values
     *
     * @return array
     */
    public function getAttributeValuesPlain()
    {
        $result = [];

        foreach ($this->getAttributeValues() as $attributeValue) {
            $actualAttributeValue = $attributeValue->getAttributeValue();
            if ($actualAttributeValue) {
                if ($actualAttributeValue instanceof \XLite\Model\AttributeValue\AttributeValueText) {
                    $value = $attributeValue->getValue();
                } else {
                    $value = $actualAttributeValue->getId();
                }

                $result[$actualAttributeValue->getAttribute()->getId()] = $value;
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * Get attribute values string
     *
     * @return string
     */
    public function getAttributeValuesAsString()
    {
        $result = [];

        foreach ($this->getAttributeValues() as $k => $value) {
            $result[] = $value->getValue();
        }

        return $result ? implode(' / ', $result) : '';
    }

    /**
     * Check - item has product attrbiute values or not
     *
     * @return boolean
     */
    public function getAttributeValuesCount()
    {
        return $this->getAttributeValues()->count();
    }

    /**
     * Return attribute values ids
     *
     * @param integer|null $limit Limit length for returned array OPTIONAL
     *
     * @return array
     */
    public function getSortedAttributeValues($limit = null)
    {
        $result = $this->getAttributeValues()->toArray();

        if ($this->getProduct()) {
             usort($result, [$this, 'sortAttributeValues']);
        }

        if ($limit !== null) {
            $result = array_slice($result, 0, $limit);
        }

        return $result;
    }

    /**
     * Sort attribute values
     *
     * @param array $a Attribute A
     * @param array $b Attribute B
     *
     * @return boolean
     */
    protected function sortAttributeValues($a, $b)
    {
        return $a->getAttributeValue()
            && $b->getAttributeValue()
            && $a->getAttributeValue()->getAttribute()
            && $b->getAttributeValue()->getAttribute()
            && $a->getAttributeValue()->getAttribute()->getPosition($this->getProduct()) >= $b->getAttributeValue()->getAttribute()->getPosition($this->getProduct());
    }

    /**
     * Check if item is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        $result = $this->getProduct()->getEnabled() && 0 < $this->getAmount();

        if ($result && $this->getProduct()->isUpcomingProduct()) {
            $result = $this->getProduct()->isAllowedUpcomingProduct();
        }

        if (
            $result
            && (
                $this->hasAttributeValues()
                || $this->getProduct()->hasEditableAttributes()
            )
        ) {
            $result = array_keys($this->getAttributeValuesIds()) == $this->getProduct()->getEditableAttributesIds();
        }

        return $result;
    }

    /**
     * Check if item is allowed to add to cart
     *
     * @return boolean
     */
    public function isConfigured()
    {
        return true;
    }

    /**
     * Check - can change item's amount or not
     *
     * @return boolean
     */
    public function canChangeAmount()
    {
        $product = $this->getProduct();

        return !$product
            || !$product->getInventoryEnabled()
            || 0 < $product->getPublicAmount();
    }

    /**
     * Check - item has valid amount or not
     *
     * @return boolean
     */
    public function isValidAmount()
    {
        return $this->checkAmount();
    }

    /**
     * Check if the item is valid to clone through the Re-order functionality
     *
     * @return boolean
     */
    public function isValidToClone()
    {
        $result = !$this->isDeleted() && $this->isValid() && $this->getProduct()->isAvailable();

        if ($result && !$this->isActualAttributes()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Return true if order item's attribute values are all up-to-date
     *
     * @return boolean
     */
    public function isActualAttributes()
    {
        $result = true;

        if ($this->hasAttributeValues()) {
            foreach ($this->getAttributeValues() as $av) {
                if (!$av->getAttributeValue()) {
                    $result = false;
                    break;
                }
            }
        } elseif ($this->getObject() && $this->getObject()->hasEditableAttributes()) {
            $result = false;
        }

        return $result;
    }

    /**
     * Set price
     *
     * @param float $price Price
     *
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;

        if ($this->itemNetPrice === null) {
            $this->setItemNetPrice($price);
        }
    }

    /**
     * Set attrbiute values
     *
     * @param array $attributeValues Attrbiute values (prepared, from request)
     *
     * @return void
     */
    public function setAttributeValues(array $attributeValues)
    {
        foreach ($this->getAttributeValues() as $av) {
            \XLite\Core\Database::getEM()->remove($av);
        }

        $this->getAttributeValues()->clear();

        foreach ($attributeValues as $av) {
            if (is_array($av)) {
                $value = $av['value'];
                $av = $av['attributeValue'];
            } else {
                $value = $av->asString();
            }

            $newValue = new \XLite\Model\OrderItem\AttributeValue();
            $newValue->setName($av->getAttribute()->getName());
            $newValue->setValue($value);
            $newValue->setAttributeId($av->getAttribute()->getId());
            $newValue->setOrderItem($this);
            $this->addAttributeValues($newValue);
            $newValue->setAttributeValue($av);
        }
    }

    /**
     * Check - item has product attrbiute values or not
     *
     * @return boolean
     */
    public function hasAttributeValues()
    {
        return 0 < $this->getAttributeValues()->count();
    }

    /**
     * Initial calculate order item
     *
     * @return void
     */
    public function calculate()
    {
        $subtotal = $this->calculateNetSubtotal();

        $this->setSubtotal($subtotal);
        $this->setDiscountedSubtotal($subtotal);
        $this->setTotal($subtotal);
    }

    /**
     * Renew order item
     *
     * @return boolean
     */
    public function renew()
    {
        $available = true;

        $product = $this->getProduct();
        if ($product) {
            if (!$product->getId()) {
                $available = false;
            } else {
                $this->setPrice($product->getDisplayPrice());
                $this->setName($product->getName());
                $this->setSku($product->getSku());
            }
        }

        return $available;
    }

    /**
     * Return true if ordered item is a valid product and is taxable
     *
     * @return boolean
     */
    public function getTaxable()
    {
        $product = $this->getProduct();

        return $product ? $product->getTaxable() : false;
    }

    /**
     * Get item taxable basis
     *
     * @return float
     */
    public function getTaxableBasis()
    {
        $product = $this->getProduct();

        return $product ? $product->getTaxableBasis() : null;
    }

    /**
     * Get product classes
     *
     * @return array
     */
    public function getProductClass()
    {
        $product = $this->getProduct();

        return $product ? $product->getClass() : null;
    }

    /**
     * Get event cell base information
     *
     * @return array
     */
    public function getEventCell()
    {
        return [
            'item_id'     => $this->getItemId(),
            'key'         => $this->getKey(),
            'object_type' => static::PRODUCT_TYPE,
            'object_id'   => $this->getProductId(),
        ];
    }

    /**
     * 'IsDeleted' flag
     *
     * @return boolean
     */
    public function isDeleted()
    {
        return !$this->getObject();
    }

    /**
     * Calculate item total
     *
     * @return float
     */
    public function calculateTotal()
    {
        $total = $this->getSubtotal();

        /** @var Surcharge $surcharge */
        foreach ($this->getExcludeSurcharges() as $surcharge) {
            if ($surcharge->getAvailable()) {
                $total += $surcharge->getValue();
            }
        }

        return $total;
    }

    /**
     * Get total with VAT
     */
    public function getDisplayTotal()
    {
        return $this->getTotal();
    }

    /**
     * Calculate net subtotal
     *
     * @return float
     */
    public function calculateNetSubtotal()
    {
        if ($this->isOrderOpen() || $this->getItemNetPrice() === null) {
            $this->setItemNetPrice($this->defineNetPrice());
        }

        return $this->getItemNetPrice() * $this->getAmount();
    }

    /**
     * Get net subtotal without round net price
     *
     * @return float
     */
    public function getNetSubtotal()
    {
        return $this->calculateNetSubtotal();
    }

    /**
     * Get inventory amount of this item
     *
     * @return int
     */
    public function getInventoryAmount()
    {
        return $this->getProduct()->getAmount();
    }

    /**
     * Increase / decrease product inventory amount
     *
     * @param integer $delta Amount delta
     *
     * @return void
     */
    public function changeAmount($delta)
    {
        $this->getProduct()->changeAmount($delta);
    }

    /**
     * Check - item price is controlled by server or not
     *
     * @return boolean
     */
    public function isPriceControlledServer()
    {
        return false;
    }

    /**
     * Define net price
     *
     * @return float
     */
    protected function defineNetPrice()
    {
        return $this->getNetPrice();
    }

    /**
     * Get deleted product
     *
     * @return \XLite\Model\Product|void
     */
    protected function getDeletedProduct()
    {
        if ($this->dumpProduct === null) {
            $this->dumpProduct = new \XLite\Model\Product();

            $this->dumpProduct->setPrice($this->getItemPrice());
            $this->dumpProduct->setName($this->getName());
            $this->dumpProduct->setSku($this->getSku());
        }

        return $this->dumpProduct;
    }

    /**
     * Check item amount
     *
     * @return boolean
     */
    protected function checkAmount()
    {
        $result = true;

        $product = $this->getProduct();
        if ($product && $product->getId()) {
            $result = !$product->getInventoryEnabled()
                || $product->getAvailableAmount() >= 0;
        }

        return $result;
    }

    /**
     * Save item state
     *
     * @param \XLite\Model\Base\IOrderItem $item Item object
     *
     * @return void
     */
    protected function saveItemState(\XLite\Model\Base\IOrderItem $item)
    {
        $price = $item->getPrice();

        $this->setPrice(\Includes\Utils\Converter::formatPrice($price));
        $this->setName($item->getName());
        $this->setSku($item->getSku());
    }

    /**
     * Reset item state
     *
     * @return void
     */
    protected function resetItemState()
    {
        $this->price = 0;
        $this->itemNetPrice = 0;
        $this->name = '';
        $this->sku = '';
    }

    /**
     * Get item_id
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->item_id;
    }

    /**
     * Get item_id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getItemId();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return OrderItem
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Set sku
     *
     * @param string $sku
     * @return OrderItem
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
        return $this;
    }

    /**
     * Get sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set itemNetPrice
     *
     * @param float $itemNetPrice
     * @return OrderItem
     */
    public function setItemNetPrice($itemNetPrice)
    {
        $this->itemNetPrice = $itemNetPrice;
        return $this;
    }

    /**
     * Set discountedSubtotal
     *
     * @param float $discountedSubtotal
     * @return OrderItem
     */
    public function setDiscountedSubtotal($discountedSubtotal)
    {
        $this->discountedSubtotal = $discountedSubtotal;
        return $this;
    }

    /**
     * Get discountedSubtotal
     *
     * @return float
     */
    public function getDiscountedSubtotal()
    {
        return $this->discountedSubtotal;
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
     * Return BackorderedAmount
     *
     * @return int
     */
    public function getBackorderedAmount()
    {
        return $this->backorderedAmount;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Get subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * Get object
     *
     * @return \XLite\Model\Product
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Get order
     *
     * @return \XLite\Model\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Add surcharges
     *
     * @param \XLite\Model\OrderItem\Surcharge $surcharges
     * @return OrderItem
     */
    public function addSurcharges(\XLite\Model\OrderItem\Surcharge $surcharges)
    {
        $this->surcharges[] = $surcharges;
        return $this;
    }

    /**
     * Get surcharges
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSurcharges()
    {
        return $this->surcharges;
    }

    /**
     * Add attributeValues
     *
     * @param \XLite\Model\OrderItem\AttributeValue $attributeValues
     * @return OrderItem
     */
    public function addAttributeValues(\XLite\Model\OrderItem\AttributeValue $attributeValues)
    {
        $this->attributeValues[] = $attributeValues;
        return $this;
    }

    /**
     * Get attributeValues
     *
     * @return \Doctrine\Common\Collections\Collection|\XLite\Model\OrderItem\AttributeValue[]
     */
    public function getAttributeValues()
    {
        return $this->attributeValues;
    }

    /**
     * Release backorder
     */
    public function releaseBackorder()
    {
        $this->setBackorderedAmount(0);
    }

    /**
     * @return bool
     */
    protected function isBackordered()
    {
        return $this->getOrder()
            && $this->getOrder()->isBackordered()
            && $this->getBackorderedAmount();
    }

    /**
     * Refresh update date
     */
    public function refreshUpdateDate()
    {
        $this->updateDate = \XLite\Core\Converter::time();
    }

    /**
     * Get update date
     *
     * @return integer
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
}
