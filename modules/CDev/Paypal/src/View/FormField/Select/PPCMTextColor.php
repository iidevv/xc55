<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

class PPCMTextColor extends \XLite\View\FormField\Select\Regular
{
    public const PPCM_BLACK      = 'black';
    public const PPCM_WHITE      = 'white';
    public const PPCM_MONOCHROME = 'monochrome';
    public const PPCM_GRAYSCALE  = 'grayscale';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_BLACK      => static::t('Black (ppcm)'),
            static::PPCM_WHITE      => static::t('White (ppcm)'),
            static::PPCM_MONOCHROME => static::t('Monochrome (ppcm)'),
            static::PPCM_GRAYSCALE  => static::t('Grayscale (ppcm)'),
        ];
    }
}
