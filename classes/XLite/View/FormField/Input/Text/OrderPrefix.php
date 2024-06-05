<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

use XLite\View\FormField\AFormField;
use XLite\View\FormField\Input\Text;

class OrderPrefix extends Text
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->setWidgetParams([
            AFormField::PARAM_LABEL => static::t('Order prefix'),
            AFormField::PARAM_NAME  => 'settings[prefix]',
            AFormField::PARAM_ID    => 'settings_prefix'
        ]);
    }
}
