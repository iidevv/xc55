<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourOrders;

use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="100")
 */
class YourOrders extends YourAccountBlock
{
    /**
     * Get your orders title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage your orders');
    }

    /**
     * Get your orders image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-orders.png';
    }

    /**
     * Get your orders url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [
            [
                'url' => $this->buildURL('order_list'),
                'text' => static::t('SkinActYourAccountPage order history and tracking'),
                'is_count' => false,
                'position' => 1
            ]
        ];
    }
}