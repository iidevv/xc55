<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Module\XC\OrdersImport\Logic\Import\Processor;

use XCart\Extender\Mapping\Extender;

/**
 * Orders
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\OrdersImport")
 */
class Orders extends \XC\OrdersImport\Logic\Import\Processor\Orders
{
    protected function detectProductBySku($sku)
    {
        if (parent::detectProductBySku($sku)) {
            return parent::detectProductBySku($sku);
        } else {
            return $this->detectProductVariantBySku($sku)
                ? $this->detectProductVariantBySku($sku)->getProduct()
                : null;
        }
    }

    /**
     * @param $sku
     *
     * @return null|\XC\ProductVariants\Model\ProductVariant
     */
    protected function detectProductVariantBySku($sku)
    {
        return \XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')->findOneBy([
            'sku' => $sku,
        ]);
    }

    protected function getItemByData($data)
    {
        $item = parent::getItemByData($data);

        if (
            $item->getObject()
            && !parent::detectProductBySku($data['itemSKU'])
            && ($variant = $this->detectProductVariantBySku($data['itemSKU']))
        ) {
            $item->setVariant($variant);
        }

        return $item;
    }
}
