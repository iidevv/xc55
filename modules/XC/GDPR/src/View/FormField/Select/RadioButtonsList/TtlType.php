<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\FormField\Select\RadioButtonsList;

/**
 * CSV delimiter
 */
class TtlType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Get default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            'session' => static::t('Until browser is closed'),
            'ttl'     => static::t('For several days after last visit'),
        ];
    }
}
