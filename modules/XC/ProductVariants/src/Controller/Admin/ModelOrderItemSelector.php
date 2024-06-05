<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * OrderItem model selector controller
 * @Extender\Mixin
 */
class ModelOrderItemSelector extends \XLite\Controller\Admin\ModelOrderItemSelector
{
    /**
     * Add selected variant to the order item
     *
     * @param \XLite\Model\OrderItem $orderItem Order item entity
     *
     * @return \XLite\Model\OrderItem
     */
    protected function postprocessOrderItem(\XLite\Model\OrderItem $orderItem)
    {
        $orderItem = parent::postprocessOrderItem($orderItem);

        if ($orderItem->getProduct()->mustHaveVariants()) {
            $variant = $orderItem->getProduct()->getVariantByAttributeValuesIds(
                $orderItem->getAttributeValuesIds()
            );

            if ($variant) {
                $orderItem->setVariant($variant);
                $orderItem->setSku($variant->getDisplaySku());
            }
        }

        return $orderItem;
    }
}
