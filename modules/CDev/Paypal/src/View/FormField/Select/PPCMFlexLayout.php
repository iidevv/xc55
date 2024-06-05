<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

class PPCMFlexLayout extends \XLite\View\FormField\Select\Regular
{
    public const PPCM_REGULAR = '8x1';
    public const PPCM_SLIM    = '20x1';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_REGULAR => static::t('Regular (ppcm)'),
            static::PPCM_SLIM    => static::t('Slim (ppcm)'),
        ];
    }
}
