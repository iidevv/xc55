<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

use XLite\View\FormField\AFormField;

class IsTestMode extends TestLiveMode
{
    /**
     * Test/Live mode values
     */
    public const LIVE = '0';
    public const TEST = '1';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->setWidgetParams([
            AFormField::PARAM_NAME  => 'settings[test]',
            AFormField::PARAM_ID    => 'settings_test',
            AFormField::PARAM_LABEL => static::t('Test/Live mode')
        ]);
    }
}
