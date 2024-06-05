<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Module\XC\ProductVariants\Model\Order\Parcel;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents a Canada Post parcel items
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\ProductVariants")
 */
class Item extends \XC\CanadaPost\Model\Order\Parcel\Item
{
    /**
     * Get object weight (in store weight units)
     *
     * @return float
     */
    protected function getObjectWeight()
    {
        /** @var \XC\ProductVariants\Model\ProductVariant $productVariant */
        $productVariant = $this->getOrderItem()->getVariant();

        return $productVariant
            ? $productVariant->getClearWeight()
            : parent::getObjectWeight();
    }
}
