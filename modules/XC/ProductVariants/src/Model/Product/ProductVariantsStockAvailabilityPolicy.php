<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\Product;

use XCart\Extender\Mapping\Extender;
use Includes\Utils\ArrayManager;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Product;
use XC\ProductVariants\Model\ProductVariant;

/**
 * @Extender\Mixin
 */
class ProductVariantsStockAvailabilityPolicy extends \XLite\Model\Product\ProductStockAvailabilityPolicy
{
    public const PRODUCT_HAS_VARIANTS        = 'product_has_variants';
    public const PRODUCT_VARIANTS            = 'product_variants';
    public const VARIANT_USE_PRODUCTS_AMOUNT = 'variant_use_products_amount';
    public const VARIANT_ID                  = 'variant_id';
    public const VARIANT_AMOUNT              = 'variant_amount';

    /**
     * Check if product is out of stock
     *
     * @param Cart $cart
     *
     * @return bool
     */
    public function isOutOfStock(Cart $cart)
    {
        if (!$this->dto[self::PRODUCT_HAS_VARIANTS]) {
            return parent::isOutOfStock($cart);
        } else {
            foreach ($this->dto[self::PRODUCT_VARIANTS] as $v) {
                if ($v[self::VARIANT_USE_PRODUCTS_AMOUNT]) {
                    if (!parent::isOutOfStock($cart)) {
                        return false;
                    }
                } elseif (!$this->isVariantOutOfStock($cart, $v[self::VARIANT_ID])) {
                    return false;
                }
            }

            return true;
        }
    }

    // TODO: Try to extract getAvailableVariantAmount() & canAddVariantToCart() into a separate ProductVariantStockAvailabilityPolicy similar to ProductStockAvailabilityPolicy. This seems especially reasonable together with converting ProductStockAvailabilityPolicy to the true value object (see doc block comment for the latter).

    /**
     * Get available amount for a specific variant
     *
     * @param Cart $cart
     * @param      $variantId
     *
     * @return int
     */
    public function getAvailableVariantAmount(Cart $cart, $variantId)
    {
        $variant = $this->dto[self::PRODUCT_VARIANTS][$variantId];

        if ($variant[self::VARIANT_USE_PRODUCTS_AMOUNT]) {
            return parent::getAvailableAmount($cart);
        } else {
            return max(0, $variant[self::VARIANT_AMOUNT]);
        }
    }


    /**
     * Get available amount for a specific variant
     *
     * @param Cart $cart
     * @param      $variantId
     *
     * @return int
     */
    public function getInCartVariantAmount(Cart $cart, $variantId)
    {
        $variant = $this->dto[self::PRODUCT_VARIANTS][$variantId];

        if ($variant[self::VARIANT_USE_PRODUCTS_AMOUNT]) {
            return parent::getInCartAmount($cart);
        } else {
            $cartItems  = $cart->getItemsByVariantId($variant[self::VARIANT_ID]);
            $cartAmount = ArrayManager::sumObjectsArrayFieldValues($cartItems, 'getAmount', true);

            return max(0, $cartAmount);
        }
    }

    /**
     * Check if specific variant is out of stock
     *
     * @param Cart $cart
     * @param      $variantId
     *
     * @return bool
     */
    public function isVariantOutOfStock(Cart $cart, $variantId)
    {
        return $this->getAvailableVariantAmount($cart, $variantId) <= 0;
    }

    /**
     * Get first variant that is available for adding to cart
     *
     * @param Cart $cart
     *
     * @return bool|null
     */
    public function getFirstAvailableVariantId(Cart $cart)
    {
        foreach ($this->dto[self::PRODUCT_VARIANTS] as $variantId => $_) {
            if (!$this->isVariantOutOfStock($cart, $variantId)) {
                return $variantId;
            }
        }

        return null;
    }

    /**
     * Create a data transfer object out of the Product instance.
     * This object should contain all of the data necessary for getAvailableAmount() & canAddToCart() methods to compute their value.
     *
     * @param Product $product
     *
     * @return array
     */
    protected function createDTO(Product $product)
    {
        $dto = parent::createDTO($product)
            + [
                self::PRODUCT_HAS_VARIANTS => false,
                self::PRODUCT_VARIANTS     => [],
            ];

        if ($product->hasVariants()) {
            $variantsDto = Database::getRepo('XC\ProductVariants\Model\ProductVariant')
                ->getProductVariantsAsDTO($product);

            $variants = array_map(static function ($v) {
                return [
                    self::VARIANT_ID                  => $v['id'],
                    self::VARIANT_USE_PRODUCTS_AMOUNT => $v['use_product_amount'],
                    self::VARIANT_AMOUNT              => $v['amount'],
                ];
            }, $variantsDto);

            $variantIds = array_map(static function ($v) {
                /** @var ProductVariant $v */
                return $v['id'];
            }, $variantsDto);

            $dto[self::PRODUCT_HAS_VARIANTS] = count($variantsDto) > 0;
            $dto[self::PRODUCT_VARIANTS] = array_combine($variantIds, $variants);
        }

        return $dto;
    }
}
