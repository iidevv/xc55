<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XLite\View\FormField\AFormField;

class TransactionType extends Regular
{
    /**
     * Transaction types: Auth and Capture & Auth only.
     */
    public const AUTH_AND_CAPTURE = 'sale';
    public const AUTH_ONLY        = 'auth';

    protected function getDefaultOptions(): array
    {
        return [
            static::AUTH_AND_CAPTURE => static::t('Auth and Capture'),
            static::AUTH_ONLY        => static::t('Auth only'),
        ];
    }

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
        $this->setWidgetParams([
            AFormField::PARAM_LABEL => static::t('Transaction type'),
            AFormField::PARAM_NAME  => 'settings[type]',
            AFormField::PARAM_ID    => 'settings_type'
        ]);
    }
}
