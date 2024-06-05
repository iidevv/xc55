<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourOrders;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("Qualiteam\SkinActProductReOrdering")
 */
class YourOrdersReorder extends YourOrders
{
    /**
     * Get your orders reorder url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('re_order'),
                    'text' => static::t('SkinActYourAccountPage reorder'),
                    'is_count' => false,
                    'position' => 5
                ]
            ]
        );
    }
}