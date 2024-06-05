<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Model\AttributeValue;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\OrderItem;
use XLite\Model\Product;
use XLite\View\Price;

/**
 * Abstract multiple attribute value
 * @ORM\MappedSuperclass
 * @Extender\Mixin
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple
{
    /**
     * Subscription fee modifier
     *
     * @var float
     *
     * @ORM\Column (type="decimal", precision=14, scale=4)
     */
    protected $subscriptionFeeModifier = 0.0000;

    /**
     * Subscription fee modifier type
     *
     * @var string
     *
     * @ORM\Column (type="string", options={ "fixed": true }, length=1)
     */
    protected $subscriptionFeeModifierType = self::TYPE_PERCENT;

    /**
     * Find product by product_id request param
     *
     * @return Product|null
     */
    public static function findProduct()
    {
        $productId = Request::getInstance()->product_id;
        $itemId = Request::getInstance()->item_id;
        $result = null;

        if ($productId) {
            /** @var Product $result */
            $result = Database::getRepo(Product::class)->find($productId);
        } elseif ($itemId && is_numeric($itemId)) {
            /** @var OrderItem $orderItem */
            $orderItem = Database::getRepo(OrderItem::class)->find($itemId);
            if ($orderItem) {
                $result = $orderItem->getProduct();
            }
        }

        return $result;
    }

    /**
     * Return modifiers
     *
     * @return array
     */
    public static function getModifiers()
    {
        $modifiers = parent::getModifiers();
        $product = static::findProduct();

        if ($product && $product->hasSubscriptionPlan()) {

            $subscriptionFeeModifier = [
                'subscriptionFee' => [
                    'title' => 'Subscr. fee',
                    'symbol' => '$',
                ],
            ];

            $modifiers = array_merge($modifiers, $subscriptionFeeModifier);
        }

        return $modifiers;
    }

    /**
     * Format modifier Subscription fee
     *
     * @param float $value Value
     *
     * @return string
     */
    public static function formatModifierSubscriptionFee($value)
    {
        return Price::getInstance()->formatPrice($value, null, true);
    }

    /**
     * Format modifier
     *
     * @param float  $value Value
     * @param string $field Field
     *
     * @return string
     */
    public static function formatModifier($value, $field)
    {
        $product = static::findProduct();
        $result = parent::formatModifier($value, $field);

        if ($product && $product->hasSubscriptionPlan()) {

            if ('subscriptionFee' == $field) {
                $result = parent::formatModifierPrice($value);
                $result .= ' to subscr.fee';
            } elseif ('price' == $field) {
                $result .= ' to setup fee';
            }
        }

        return $result;
    }

    /**
     * Get modifier base value
     *
     * @param string $field Field
     *
     * @return float
     */
    protected function getModifierBase($field)
    {
        if ('subscriptionFee' == $field) {
            $result = $this->getModifierBaseSubscriptionFee();
        } else {
            $result = parent::getModifierBase($field);
        }

        return $result;
    }

    /**
     * Set subscriptionFee modifier
     *
     * @param float $subscriptionFeeModifier
     */
    public function setSubscriptionFeeModifier($subscriptionFeeModifier)
    {
        $this->subscriptionFeeModifier = $subscriptionFeeModifier;
    }

    /**
     * Get subscriptionFee modifier
     *
     * @return float
     */
    public function getSubscriptionFeeModifier()
    {
        return $this->subscriptionFeeModifier;
    }

    /**
     * Set subscriptionFeeModifierType
     *
     * @param string $subscriptionFeeModifierType
     */
    public function setSubscriptionFeeModifierType($subscriptionFeeModifierType)
    {
        $this->subscriptionFeeModifierType = $subscriptionFeeModifierType;
    }

    /**
     * Get subscriptionFeeModifierType
     *
     * @return string
     */
    public function getSubscriptionFeeModifierType()
    {
        return $this->subscriptionFeeModifierType;
    }

    /**
     * Get modifierBaseSubscriptionFee
     *
     * @return float
     */
    protected function getModifierBaseSubscriptionFee()
    {
        return $this->getProduct()->getClearFeePrice();
    }
}
