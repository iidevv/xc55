<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourOrders;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActAftership")
 */
class YourOrdersOrderTracking extends YourOrders
{
    /**
     * Get your orders order tracking url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('trackings'),
                    'text' => static::t('SkinActYourAccountPage order tracking'),
                    'is_count' => false,
                    'position' => 2
                ]
            ]
        );
    }
}