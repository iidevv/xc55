<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select category show mode
 */
class EnabledDisabled extends \XLite\View\FormField\Select\Regular
{
    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === true || $value === '1' || $value === 'Y') {
            $value = 1;
        } elseif ($value === false || $value === '0' || $value === 'N') {
            $value = 0;
        }

        return $value;
    }

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            1 => static::t('Enabled'),
            0 => static::t('Disabled'),
        ];
    }
}
