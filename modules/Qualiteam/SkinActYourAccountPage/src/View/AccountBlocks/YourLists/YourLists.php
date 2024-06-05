<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourLists;

use XCart\Extender\Mapping\ListChild;
use Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourAccountBlock;

/**
 * @ListChild (list="account-blocks.element", zone="customer", weight="101")
 */
class YourLists extends YourAccountBlock
{
    /**
     * Get your lists title
     *
     * @return string
     */
    protected function getBlockTitle(): string
    {
        return static::t('SkinActYourAccountPage your lists');
    }

    /**
     * Get your lists image
     *
     * @return string
     */
    protected function getBlockImage(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/i-your-lists.png';
    }

    /**
     * Get your lists url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return [
            [
                'url' => $this->buildURL('cart'),
                'text' => static::t('SkinActYourAccountPage shopping cart'),
                'is_count' => false,
                'position' => 1
            ]
        ];
    }
}