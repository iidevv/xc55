<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\Core;

/**
 * Proxy class for wrapping getter methods to Product or ProductVariant object.
 */
class FeedItem extends \XLite\Base
{
    /**
     * Product associated with the feed item.
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Constructor.
     *
     * @param \XLite\Model\Product $product Product to associate with the feed item.
     */
    public function __construct(\XLite\Model\Product $product)
    {
        $this->setProduct($product);
    }

    /**
     * Associate the feed item with a product.
     *
     * @param \XLite\Model\Product $product Product to associate with the feed item.
     *
     * @return void
     */
    public function setProduct(\XLite\Model\Product $product)
    {
        $this->product = $product;
    }

    /**
     * Get the product associated with the feed item.
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Get a value of the item field.
     *
     * @param string $field Name of the field.
     *
     * @return mixed
     */
    public function getFieldValue($field)
    {
        return call_user_func([$this, 'get' . ucfirst($field)]);
    }

    /**
     * Magic method that redirects getter calls to the associated product object.
     *
     * @param string $method Method name
     * @param array  $args   Method parameters OPTIONAL
     *
     * @return mixed
     * @throws \BadMethodCallException
     *
     */
    public function __call($method, array $args = [])
    {
        $func = [$this->getProduct(), $method];
        if (is_callable($func)) {
            return call_user_func_array($func, $args);
        }

        throw new \BadMethodCallException('Method ' . $method . ' is not callable in ' . get_class($this));
    }

    /**
     * Get a value of the item attribute.
     *
     * @param \XLite\Model\Attribute $attribute Attribute instance.
     *
     * @return mixed
     */
    public function getAttributeValue(\XLite\Model\Attribute $attribute)
    {
        $value =  $attribute->getAttributeValue($this->getProduct(), true);

        return (is_array($value) && !empty($value)) ? array_pop($value) : null;
    }

    /**
     * Get the product quantity in stock.
     *
     * @return integer
     */
    public function getAvailableAmount()
    {
        return $this->getProduct()->getPublicAmount();
    }

    /**
     * Check whether the product is out of stock, or not.
     *
     * @return boolean
     */
    public function isOutOfStock()
    {
        return $this->getProduct()->isOutOfStock();
    }

    /**
     * Get weight of the item in specified units.
     *
     * @param string $units Units OPTIONAL
     *
     * @return string
     */
    public function getWeight($units = 'lbs')
    {
        return \XLite\Core\Converter::convertWeightUnits(
            $this->getProduct()->getWeight(),
            \XLite\Core\Config::getInstance()->Units->weight_unit,
            $units
        );
    }
}
