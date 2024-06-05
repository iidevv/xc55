<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\DataSet\Transport\Order;

/**
 * Surcharge info
 * @property string $name
 * @property string $notAvailableReason
 */
class Surcharge extends \XLite\DataSet\Transport\ATransport
{
    /**
     * Define keys
     *
     * @return array
     */
    protected function defineKeys()
    {
        return [
            'name',
            'notAvailableReason',
        ];
    }
}
