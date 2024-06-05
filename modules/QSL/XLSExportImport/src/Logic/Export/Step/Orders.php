<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\XLSExportImport\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Orders export step
 * @Extender\Mixin
 */
class Orders extends \XLite\Logic\Export\Step\Orders
{
    /**
     * @inheritdoc
     */
    protected function getWriterTypes()
    {
        $types = parent::getWriterTypes();

        $types['integers'][] = static::ITEM_PREFIX . 'Quantity';

        $types['currencies'][] = static::ITEM_PREFIX . 'Price';
        $types['currencies'][] = static::ITEM_PREFIX . 'Subtotal';
        $types['currencies'][] = static::ITEM_PREFIX . 'Total';
        $types['currencies'][] = 'total';
        $types['currencies'][] = static::PAYMENT_TRANSACTION_PREFIX . 'Value';

        foreach ($this->getOrderItemSurchargeTypes() as $type) {
            $types['currencies'][] = $type['name'] . ' (item surcharge)';
        }

        foreach ($this->getOrderSurchargeTypes() as $type) {
            $types['currencies'][] = $type['name'] . ' (surcharge)';
        }

        $types['dates'][] = 'date';

        return $types;
    }
}
