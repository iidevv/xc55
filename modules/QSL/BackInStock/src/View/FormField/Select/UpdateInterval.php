<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\FormField\Select;

/**
 * Interval selector
 */
class UpdateInterval extends \XLite\View\FormField\Select\Regular
{
    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return \XLite\Core\Config::getInstance()->QSL->BackInStock->updateInterval;
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultOptions()
    {
        return [
            900   => static::t('15 minutes'),
            1800  => static::t('30 minutes'),
            3600  => static::t('1 hour'),
            7200  => static::t('2 hours'),
            14400 => static::t('4 hours'),
            21600 => static::t('6 hours'),
            43200 => static::t('12 hours'),
            86400 => static::t('1 day'),
        ];
    }
}
