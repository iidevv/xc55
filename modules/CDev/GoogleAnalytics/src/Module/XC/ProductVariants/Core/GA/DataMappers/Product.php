<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\ProductVariants\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;
use XC\ProductVariants\Model\ProductVariant;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Product extends \CDev\GoogleAnalytics\Core\GA\DataMappers\Product
{
    public function getAddProductData(\XLite\Model\Product $product, string $listName = '', string $positionInList = '', string $coupon = '', $qty = null): array
    {
        $result = parent::getAddProductData($product, $listName, $positionInList, $coupon, $qty);

        /** @var \XC\ProductVariants\Model\Product $product */
        $variant = $product->getVariant();

        return static::mapVariantData($variant, $result);
    }

    protected static function getVariant(\XLite\Model\Product $product): string
    {
        $variantName = parent::getVariant($product);

        /** @var \XC\ProductVariants\Model\Product $product */
        $variant = $product->getVariant();

        return self::getVariantTitle($variant, $variantName);
    }

    public static function getVariantTitle(?ProductVariant $variant, string $variantName): string
    {
        if ($variant) {
            $hash = [];
            foreach ($variant->getValues() as $av) {
                $hash[] = $av->getAttribute()->getName() . ':' . $av->asString();
            }
            sort($hash);
            $variantName = implode('_', $hash);
        }

        return $variantName;
    }

    public static function mapVariantData(?ProductVariant $variant, array $result): array
    {
        if ($variant) {
            $result['id']    = $variant->getSku() ?: $result['id'];
            $result['price'] = is_numeric($variant->getNetPrice())
                ? $variant->getNetPrice()
                : $result['price'];
        }

        return $result;
    }
}
