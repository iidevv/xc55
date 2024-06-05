<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

class EmailFrom extends \XLite\View\FormField\Select\Regular
{
    public const OPTION_FROM_CONTACT = 'contact';
    public const OPTION_FROM_SERVER  = 'server';
    public const OPTION_MANUAL       = 'manual';

    protected function getDefaultOptions()
    {
        return [
            static::OPTION_FROM_CONTACT => static::t('email from Contact information section'),
            static::OPTION_FROM_SERVER => static::t('sender identified by server'),
            static::OPTION_MANUAL => static::t('specific email address (set up below)'),
        ];
    }

    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'form_field/select/email_from.js'
        ]);
    }
}
