<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\FormField;

/**
 * Gift Tier widget look selector
 */
class GiftTierLook extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'list'  => static::t('List'),
            'grid'  => static::t('Grid'),
            'table' => static::t('Table'),
        ];
    }
}
