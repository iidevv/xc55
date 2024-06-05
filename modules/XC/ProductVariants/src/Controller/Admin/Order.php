<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product variants page controller (Order section)
 * @Extender\Mixin
 */
class Order extends \XLite\Controller\Admin\Order
{
    /**
     * Prepare order item before price calculation
     *
     * @param \XLite\Model\OrderItem $item       Order item
     * @param array                  $attributes Attributes
     *
     * @return void
     */
    protected function prepareItemBeforePriceCalculation(\XLite\Model\OrderItem $item, array $attributes)
    {
        parent::prepareItemBeforePriceCalculation($item, $attributes);

        if ($item && $item->getProduct()->mustHaveVariants()) {
            $variant = $item->getProduct()->getVariantByAttributeValuesIds($attributes);
            if ($variant) {
                $oldVariant = $item->getVariant();
                if (!$oldVariant || $oldVariant->getId() != $variant->getId()) {
                    \XLite\Core\Request::getInstance()->oldAmount = null;
                }
                $item->setVariant($variant);
            }
        }

        return $item;
    }

    /**
     * Assemble recalculate item event
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return array
     */
    protected function assembleRecalculateItemEvent(\XLite\Model\OrderItem $item)
    {
        $data = parent::assembleRecalculateItemEvent($item);

        if ($item->getVariant()) {
            $data['sku'] = $item->getVariant()->getSku()
                ? $item->getVariant()->getSku()
                : null;
        }

        return $data;
    }
}
