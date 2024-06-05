<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XLite\View\FormField\AFormField;

class TestLiveModeSetting extends TestLiveMode
{
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();
        $this->setWidgetParams([
            AFormField::PARAM_LABEL => static::t('Test/Live mode'),
            AFormField::PARAM_ID    => 'settings_mode',
            AFormField::PARAM_NAME  => 'settings[mode]'
        ]);
    }
}
