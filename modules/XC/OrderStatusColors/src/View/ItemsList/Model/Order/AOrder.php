<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\OrderStatusColors\View\ItemsList\Model\Order;

use XCart\Extender\Mapping\Extender;
use XLite\Model\AEntity;

/**
 * Abstract order-based list
 * @Extender\Mixin
 */
abstract class AOrder extends \XLite\View\ItemsList\Model\Order\AOrder
{
    /**
     * Get line attributes
     *
     * @param integer $index Line index
     * @param AEntity $entity Line entity OPTIONAL
     *
     * @return array
     */
    protected function getLineAttributes($index, AEntity $entity = null)
    {
        $attributes = parent::getLineAttributes($index, $entity);

        $color = $entity ? $entity->getStatusColor() : null;

        if ($color) {
            $attributes['style'] = ($attributes['style'] ?? '') . " background-color: #$color;";
        }

        return $attributes;
    }
}
