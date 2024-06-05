<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\Helpers;

use XLite\Model\OrderItem;

trait OrderItemTrait
{
    public function getUniqueProductId(OrderItem $item)
    {
        if ($item->getProduct()->hasVariants()) {
            return $item->getProduct()->getProductId() . "_" . $this->getProductVariantId($item);
        }

        return $item->getProduct()->getProductId();
    }

    public function getProductVariantId(OrderItem $item)
    {
        return $item->getVariant()->getId();
    }

    public function getProductVariantName(OrderItem $item)
    {
        $attr = [];

        foreach ($item->getSortedAttributeValues() as $value) {
            $attr[] = $value->getActualName() . ":" . $value->getActualValue();
        }

        return implode(', ', $attr);
    }
}