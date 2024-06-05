<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks\YourLists;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
class YourListsWishlist extends YourLists
{
    /**
     * Get your lists wishlist url, text and is count flag
     *
     * @return array
     */
    protected function getBlockLinks(): array
    {
        return array_merge(
            parent::getBlockLinks(),
            [
                [
                    'url' => $this->buildURL('wishlist'),
                    'text' => static::t('SkinActYourAccountPage wishlist'),
                    'is_count' => false,
                    'position' => 2
                ]
            ]
        );
    }
}