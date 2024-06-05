<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Cart page controller
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Correct product amount to add to cart
     *
     * @param \XLite\Model\OrderItem $item   Product to add
     * @param integer                $amount Amount of product
     *
     * @return integer
     */
    protected function correctAmountToAdd(\XLite\Model\OrderItem $item, $amount)
    {
        if ($item && $item->getProduct()->mustHaveVariants()) {
            $item->setVariant(
                $item->getProduct()->getVariantByAttributeValuesIds(
                    $item->getAttributeValuesIds()
                )
            );
        }

        return parent::correctAmountToAdd($item, $amount);
    }

    /**
     * Check if the requested amount is available for the product
     *
     * @param \XLite\Model\OrderItem $item   Order item to add
     * @param integer                $amount Amount to check OPTIONAL
     *
     * @return integer
     */
    protected function checkAmount(\XLite\Model\OrderItem $item, $amount = null)
    {
        return $item->getVariant() && !$item->getVariant()->getDefaultAmount()
            ? ($amount ?: 0) < $item->getVariant()->getAvailableAmount()
            : parent::checkAmount($item, $amount);
    }

    /**
     * Correct product amount to add to cart.
     *
     * @param \XLite\Model\Product $product Product to add
     * @param integer|null         $amount  Amount of product
     *
     * @return integer
     */
    protected function correctAmountAsProduct(\XLite\Model\Product $product, $amount)
    {
        if (is_null($amount) && $product->mustHaveVariants()) {
            $amount = 1;
        } else {
            $amount = parent::correctAmountAsProduct($product, $amount);
        }

        return $amount;
    }
}
