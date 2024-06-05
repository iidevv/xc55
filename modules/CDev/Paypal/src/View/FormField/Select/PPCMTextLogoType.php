<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

class PPCMTextLogoType extends \XLite\View\FormField\Select\Regular
{
    public const PPCM_PRIMARY     = 'primary';
    public const PPCM_ALTERNATIVE = 'alternative';
    public const PPCM_INLINE      = 'inline';
    public const PPCM_NONE        = 'none';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_PRIMARY     => static::t('Stacked'),
            static::PPCM_ALTERNATIVE => static::t('Single line logo'),
            static::PPCM_INLINE      => static::t('Inline logo'),
            static::PPCM_NONE        => static::t('No logo'),
        ];
    }
}
