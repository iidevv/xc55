<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\View\FormField\Select;

class StickerDisplayMode extends \XLite\View\FormField\Select\Regular
{
    public const MODE_CORNER_RIBBON = 'corner_ribbon';
    public const MODE_CLASSIC_LABEL = 'classic_label';

    /**
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::MODE_CORNER_RIBBON => static::t('Corner ribbon'),
            static::MODE_CLASSIC_LABEL => static::t('Classic label'),
        ];
    }
}
