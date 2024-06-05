<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Module\XC\ProductVariants\Core\GA\DataMappers;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class OrderItem extends \CDev\GoogleAnalytics\Core\GA\DataMappers\OrderItem
{
    public function getData(\XLite\Model\OrderItem $item): array
    {
        $result = parent::getData($item);

        /** @var \XC\ProductVariants\Model\OrderItem $item */
        $variant = $item->getVariant();

        return Product::mapVariantData($variant, $result);
    }

    protected static function getVariant(\XLite\Model\OrderItem $item): string
    {
        $variantName = parent::getVariant($item);

        /** @var \XC\ProductVariants\Model\OrderItem $item */
        $variant = $item->getVariant();

        return Product::getVariantTitle(
            $variant,
            $variantName
        );
    }
}
