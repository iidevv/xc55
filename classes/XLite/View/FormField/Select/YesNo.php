<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

/**
 * Select "Yes / No"
 */
class YesNo extends \XLite\View\FormField\Select\Regular
{
    /**
     * Yes/No mode values
     */
    public const YES = 'Y';
    public const NO  = 'N';

    /**
     * getDefaultOptions
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        return [
            static::YES => static::t('Yes'),
            static::NO  => static::t('No'),
        ];
    }

    /**
     * Return field value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if ($value === true || $value === '1' || $value === 1) {
            $value = static::YES;
        } elseif ($value === false || $value === '0' || $value === 0) {
            $value = static::NO;
        }

        return $value;
    }
}
