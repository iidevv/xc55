<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\FormField\Select;

class PPCMBannerType extends \XLite\View\FormField\Select\Regular
{
    public const PPCM_TEXT = 'text';
    public const PPCM_FLEX = 'flex';

    /**
     * Get default options
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::PPCM_TEXT => static::t('Text (ppcm)'),
            static::PPCM_FLEX => static::t('Flex (ppcm)'),
        ];
    }
}
