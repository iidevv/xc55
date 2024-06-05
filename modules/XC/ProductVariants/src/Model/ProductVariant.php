<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantInput as Input;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantUpdate as Update;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantOutput as Output;
use XC\ProductVariants\Controller\API\ProductVariant\Delete;
use XC\ProductVariants\Controller\API\ProductVariant\Put;
use XC\ProductVariants\Model\Product\ProductVariantsStockAvailabilityPolicy;
use XLite\Model\Cart;

/**
 * Product variant
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_variants")
 *
 * @ORM\HasLifecycleCallbacks
 * @ApiPlatform\ApiResource(
 *     shortName="Product Variant",
 *     itemOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/variants/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "put"={
 *              "method"="PUT",
 *              "path"="/products/{product_id}/variants/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "controller"=Put::class,
 *              "input"=Update::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/products/{product_id}/variants/{id}.{_format}",
 *              "identifiers"={"product_id", "id"},
 *              "requirements"={"product_id"="\d+", "id"="\d+"},
 *              "controller"=Delete::class,
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     },
 *     collectionOperations={
 *          "get"={
 *              "method"="GET",
 *              "path"="/products/{product_id}/variants.{_format}",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  },
 *              }
 *          },
 *          "post"={
 *              "method"="POST",
 *              "path"="/products/{product_id}/variants.{_format}",
 *              "controller"="xcart.api.xc.product_variants.product_variant.controller",
 *              "identifiers"={"product_id"},
 *              "requirements"={"product_id"="\d+"},
 *              "input"=Input::class,
 *              "output"=Output::class,
 *              "openapi_context"={
 *                  "parameters"={
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *              }
 *          }
 *     }
 * )
 */
class ProductVariant extends \XLite\Model\AEntity
{
    /**
     * Unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Product
     *
     * @var \XLite\Model\Product
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Price
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $price = 0.0000;

    /**
     * Default price flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $defaultPrice = true;

    /**
     * Amount
     *
     * @var integer
     *
     * @ORM\Column (type="integer", options={ "unsigned": true })
     */
    protected $amount = 0;

    /**
     * Default amount flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $defaultAmount = true;

    /**
     * Weight
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */

    protected $weight = 0.0000;

    /**
     * Default weight flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $defaultWeight = true;

    /**
     * Product SKU
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $sku;

    /**
     * Product variant unique id
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $variant_id;

    /**
     * Default flag
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $defaultValue = false;

    /**
     * Image
     *
     * @var \XC\ProductVariants\Model\Image\ProductVariant\Image
     *
     * @ORM\OneToOne  (targetEntity="XC\ProductVariants\Model\Image\ProductVariant\Image", mappedBy="product_variant", cascade={"all"})
     */
    protected $image;

    /**
     * Attribute value (checkbox)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueCheckbox", inversedBy="variants")
     * @ORM\JoinTable (
     *      name="product_variant_attribute_value_checkbox",
     *      joinColumns={@ORM\JoinColumn (name="variant_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="attribute_value_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $attributeValueC;

    /**
     * Attribute value (select)
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\AttributeValue\AttributeValueSelect", inversedBy="variants")
     * @ORM\JoinTable (
     *      name="product_variant_attribute_value_select",
     *      joinColumns={@ORM\JoinColumn (name="variant_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="attribute_value_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $attributeValueS;

    /**
     * Product order items
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\OrderItem", mappedBy="variant")
     */
    protected $orderItems;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->attributeValueC = new \Doctrine\Common\Collections\ArrayCollection();
        $this->attributeValueS = new \Doctrine\Common\Collections\ArrayCollection();
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get attribute value
     *
     * @param \XLite\Model\Attribute $attribute Attribute
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Attribute $attribute)
    {
        $result = null;

        foreach ($this->getValues() as $v) {
            if ($v->getAttribute()->getId() == $attribute->getId()) {
                $result = $v;
                break;
            }
        }

        return $result;
    }

    /**
     * Get attribute values
     *
     * @return array
     */
    public function getValues()
    {
        return array_merge(
            $this->getAttributeValueS()->toArray(),
            $this->getAttributeValueC()->toArray()
        );
    }

    /**
     * Increase / decrease product inventory amount
     *
     * @param integer $delta Amount delta
     */
    public function changeAmount($delta)
    {
        if (!$this->getDefaultAmount()) {
            $this->setAmount($this->getAmount() + $delta);
        }
    }

    /**
     * Get attribute values hash
     *
     * @return string
     */
    public function getValuesHash()
    {
        $hash = [];
        foreach ($this->getValues() as $av) {
            $hash[] = $av->getAttribute()->getId() . '_' . $av->getId();
        }
        sort($hash);

        return md5(implode('_', $hash));
    }

    /**
     * Get quick data price
     *
     * @return float
     */
    public function getQuickDataPrice()
    {
        return $this->getClearPrice();
    }

    /**
     * Get clear price
     *
     * @return float
     */
    public function getClearPrice()
    {
        return $this->getDefaultPrice()
            ? $this->getProduct()->getPrice()
            : $this->getPrice();
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
     * Get clear weight
     *
     * @return float
     */
    public function getClearWeight()
    {
        return $this->getDefaultWeight()
            ? $this->getProduct()->getWeight()
            : $this->getWeight();
    }

    /**
     * Get display sku
     *
     * @return float
     */
    public function getDisplaySku()
    {
        return $this->getSku() ?: $this->getProduct()->getSku();
    }

    /**
     * Get SKU
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku !== null ? (string)$this->sku : null;
    }

    /**
     * Set sku and trim it to max length
     *
     * @param string $sku
     */
    public function setSku($sku)
    {
        $this->sku = substr($sku, 0, \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')->getFieldInfo('sku', 'length'));
    }

    /**
     * Return VariantId
     *
        @return string
     */
    public function getVariantId()
    {
        return $this->variant_id;
    }

    /**
     * Set VariantId
     *
     * @param string $variant_id
     *
     * @return $this
     */
    public function setVariantId($variant_id)
    {
        $this->variant_id = $variant_id;
        return $this;
    }

    /**
     * Set needProcess to related product
     *
     * @param boolean $needProcess
     *
     * @return \XLite\Model\Product
     */
    public function setNeedProcess($needProcess)
    {
        if ($this->getProduct()) {
            $this->getProduct()->setNeedProcess($needProcess);
        }

        return $this->getProduct();
    }

    /**
     * Check if the product is out-of-stock
     *
     * @return boolean
     */
    public function isShowStockWarning()
    {
        return $this->getProduct()
            && $this->getProduct()->getInventoryEnabled()
            && $this->getProduct()->getLowLimitEnabledCustomer()
            && ($this->getPublicAmount() <= $this->getProduct()->getLowLimitAmount())
            && !$this->isOutOfStock();
    }

    /**
     * Return true if product variant can be purchased
     *
     * @return boolean
     */
    public function isAvailable()
    {
        return $this->availableInDate()
            && !$this->isOutOfStock();
    }

    /**
     * Flag if the product is available according date/time
     *
     * @return boolean
     */
    public function availableInDate()
    {
        return $this->getProduct()
            ? $this->getProduct()->availableInDate()
            : true;
    }

    /**
     * Alias: is product in stock or not
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        /** @var ProductVariantsStockAvailabilityPolicy $availabilityPolicy */
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        return !$availabilityPolicy->getAvailableVariantAmount(Cart::getInstance(), $this->getId());
    }

    /**
     * Return public amount
     *
     * @return integer
     */
    public function getPublicAmount()
    {
        return $this->getDefaultAmount()
            ? $this->getProduct()->getPublicAmount()
            : $this->getAmount();
    }

    /**
     * Return product amount available to add to cart
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        /** @var ProductVariantsStockAvailabilityPolicy $availabilityPolicy */
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        return $availabilityPolicy->getAvailableVariantAmount(Cart::getInstance(), $this->getId());
    }

    /**
     * Return max possibly product amount available to add to cart
     *
     * @return integer
     */
    public function getMaxAmount()
    {
        return $this->getAvailableAmount();
    }

    /**
     * How many product items added to cart
     *
     * @return boolean
     */
    public function getItemsInCart()
    {
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        return $availabilityPolicy->getInCartVariantAmount(Cart::getInstance(), $this->getId());
    }

    /**
     * How many product items added to cart
     *
     * @return boolean
     */
    public function getItemsInCartMessage()
    {
        $availabilityPolicy = $this->getProduct()->getStockAvailabilityPolicy();

        $count = $availabilityPolicy->getInCartVariantAmount(Cart::getInstance(), $this->getId());

        return \XLite\Core\Translation::getInstance()->translate(
            'Items with selected options in your cart: X',
            ['count' => $count]
        );
    }

    /**
     * Alias: is all product items in cart
     *
     * @return boolean
     */
    public function isAllStockInCart()
    {
        return $this->getAvailableAmount() <= $this->getItemsInCart();
    }

    /**
     * Clone
     *
     * @return \XLite\Model\AEntity
     */
    public function cloneEntity()
    {
        $newEntity = parent::cloneEntity();

        if ($this->getSku()) {
            $newEntity->setSku(
                \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                    ->assembleUniqueSKU($this->getSku())
            );
        }

        $newEntity->setVariantId(
            \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                    ->assembleUniqueVariantId($this->getVariantId())
        );

        $this->cloneEntityImage($newEntity);

        return $newEntity;
    }

    /**
     * Clone entity (image)
     *
     * @param \XC\ProductVariants\Model\ProductVariant $newEntity New entity
     */
    public function cloneEntityImage(\XC\ProductVariants\Model\ProductVariant $newEntity)
    {
        if ($this->getImage()) {
            $newImage = $this->getImage()->cloneEntity();
            $newImage->setProductVariant($newEntity);
            $newEntity->setImage($newImage);
        }
    }

    /**
     * Return taxable
     *
     * @return boolean
     */
    public function getTaxable()
    {
        return $this->getProduct()->getTaxable();
    }

    /**
     * Check if product amount is less than its low limit
     *
     * @return boolean
     */
    public function isLowLimitReached()
    {
        /** @var \XLite\Model\Product $product */
        $product = $this->getProduct();

        return $product->getLowLimitEnabled() && $this->getPublicAmount() <= $product->getLowLimitAmount();
    }

    /**
     * List of controllers which should not send notifications
     *
     * @return array
     */
    protected function getForbiddenControllers()
    {
        return [
            '\XLite\Controller\Admin\EventTask',
            '\XLite\Controller\Admin\ProductList',
            '\XLite\Controller\Admin\Product',
        ];
    }

    /**
     * Check if notifications should be sent in current situation
     *
     * @return boolean
     */
    protected function isShouldSend()
    {
        $currentController = \XLite::getInstance()->getController();
        $isControllerForbidden = array_reduce(
            $this->getForbiddenControllers(),
            static function ($carry, $controllerName) use ($currentController) {
                return $carry ?: ($currentController instanceof $controllerName);
            },
            false
        );
        return
            \XLite\Core\Request::getInstance()->event !== 'import'
            && !$isControllerForbidden;
    }

    /**
     * @ORM\PostUpdate
     */
    public function processPostUpdate()
    {
        if ($this->isLowLimitReached() && $this->isShouldSend()) {
            $this->sendLowLimitNotification();
        }
    }

    /**
     * @ORM\PrePersist
     */
    public function processPrePersist()
    {
        if (!$this->getVariantId()) {
            $this->setVariantId($this->getRepository()->assembleUniqueVariantId($this));
        }
    }

    /**
     * Send notification to admin about product low limit
     */
    protected function sendLowLimitNotification()
    {
        \XLite\Core\Mailer::sendLowVariantLimitWarningAdmin(
            $this->prepareDataForNotification()
        );
    }

    /**
     * Prepare data for 'low limit warning' email notifications
     *
     * @return array
     */
    public function prepareDataForNotification()
    {
        $data = [];

        $product = $this->getProduct();

        $data['product'] = $product;
        $data['name'] = $product->getName();
        $data['attributes'] = $this->prepareAttributesForEmail();
        $data['sku'] = $this->getDisplaySku();
        $data['amount'] = $this->getAmount();
        $data['variantsTabUrl'] = $this->getUrlToVariant();

        return $data;
    }

    /**
     * Prepare attributes for view
     *
     * @return array
     */
    protected function prepareAttributesForEmail()
    {
        $attrs = [];
        foreach ($this->getValues() as $attributeValue) {
            if ($attributeValue->getAttribute()->isVariable($this->getProduct())) {
                $attrs[] = [
                    'name'  => $attributeValue->getAttribute()->getName(),
                    'value' => $attributeValue->asString(),
                ];
            }
        }

        return $attrs;
    }

    /**
     * Get url to variants tab
     *
     * @return string
     */
    protected function getUrlToVariant()
    {
        $params = [
            'product_id' => $this->getProduct()->getProductId(),
            'page'       => 'variants',
        ];
        $fullUrl = \XLite\Core\Converter::buildFullURL(
            'product',
            '',
            $params,
            \XLite::getAdminScript()
        );

        $hashForUrl = sprintf('#data-%d-amount', $this->getId());

        return $fullUrl . $hashForUrl;
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
     * Set price
     *
     * @param float $price
     * @return ProductVariant
     */
    public function setPrice($price)
    {
        $this->price = $price;
        return $this;
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
     * Set defaultPrice
     *
     * @param boolean $defaultPrice
     * @return ProductVariant
     */
    public function setDefaultPrice($defaultPrice)
    {
        $this->defaultPrice = $defaultPrice;
        return $this;
    }

    /**
     * Get defaultPrice
     *
     * @return boolean
     */
    public function getDefaultPrice()
    {
        return $this->defaultPrice;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     * @return ProductVariant
     */
    public function setAmount($amount)
    {
        $this->amount = $this->correctAmount($amount);
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
     * Set defaultAmount
     *
     * @param boolean $defaultAmount
     * @return ProductVariant
     */
    public function setDefaultAmount($defaultAmount)
    {
        $this->defaultAmount = $defaultAmount;
        return $this;
    }

    /**
     * Get defaultAmount
     *
     * @return boolean
     */
    public function getDefaultAmount()
    {
        return $this->defaultAmount;
    }

    /**
     * Set weight
     *
     * @param float $weight
     * @return ProductVariant
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set defaultWeight
     *
     * @param boolean $defaultWeight
     * @return ProductVariant
     */
    public function setDefaultWeight($defaultWeight)
    {
        $this->defaultWeight = $defaultWeight;
        return $this;
    }

    /**
     * Get defaultWeight
     *
     * @return boolean
     */
    public function getDefaultWeight()
    {
        return $this->defaultWeight;
    }

    /**
     * Set defaultValue
     *
     * @param boolean $defaultValue
     * @return ProductVariant
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Get defaultValue
     *
     * @return boolean
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     * @return ProductVariant
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set image
     *
     * @param \XC\ProductVariants\Model\Image\ProductVariant\Image $image
     * @return ProductVariant
     */
    public function setImage(\XC\ProductVariants\Model\Image\ProductVariant\Image $image = null)
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Get image
     *
     * @return \XC\ProductVariants\Model\Image\ProductVariant\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add attributeValueC
     *
     * @param \XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC
     * @return ProductVariant
     */
    public function addAttributeValueC(\XLite\Model\AttributeValue\AttributeValueCheckbox $attributeValueC)
    {
        $this->attributeValueC[] = $attributeValueC;
        return $this;
    }

    /**
     * Get attributeValueC
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeValueC()
    {
        return $this->attributeValueC;
    }

    /**
     * Add attributeValueS
     *
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS
     * @return ProductVariant
     */
    public function addAttributeValueS(\XLite\Model\AttributeValue\AttributeValueSelect $attributeValueS)
    {
        $this->attributeValueS[] = $attributeValueS;
        return $this;
    }

    /**
     * Get attributeValueS
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributeValueS()
    {
        return $this->attributeValueS;
    }

    /**
     * Add orderItems
     *
     * @param \XLite\Model\OrderItem $orderItems
     * @return ProductVariant
     */
    public function addOrderItems(\XLite\Model\OrderItem $orderItems)
    {
        $this->orderItems[] = $orderItems;
        return $this;
    }

    /**
     * Get orderItems
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Check and (if needed) correct amount value
     *
     * @param integer $amount Value to check
     *
     * @return integer
     */
    protected function correctAmount($amount)
    {
        return max(0, (int) $amount);
    }
}
