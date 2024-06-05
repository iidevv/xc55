<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Module\XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated shipping status items list
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\CustomOrderStatuses")
 */
class Shipping extends \XC\CustomOrderStatuses\View\ItemsList\Model\Order\Status\Shipping
{
    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return array_merge(
            parent::defineColumns(),
            [
                'isReturnRequestAllowed' => [
                    static::COLUMN_NAME => static::t('Allow returns'),
                    static::COLUMN_CLASS  => '\XLite\View\FormField\Inline\Input\Checkbox\Switcher\YesNo',
                    static::COLUMN_PARAMS => [],
                    static::COLUMN_ORDERBY  => 350,
                ]
            ]
        );
    }
}
