<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Select;

class UpgradeWave extends \XLite\View\FormField\Select\Regular
{
    public function getCSSFiles(): array
    {
        $list   = parent::getCSSFiles();
        $list[] = 'form_field/waves/style.css';

        return $list;
    }

    public function getJSFiles(): array
    {
        $list   = parent::getJSFiles();
        $list[] = 'form_field/waves/controller.js';

        return $list;
    }

    protected function getFieldTemplate(): string
    {
        return 'wave_selector.twig';
    }

    protected function getHelpMessage(): string
    {
        return static::t('Upgrade access level tooltip message');
    }
}
