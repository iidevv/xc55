<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\View\FormField\Label;

use Qualiteam\SkinActYotpoReviews\Module;

class SettingsWarning extends \XLite\View\FormField\Label\ALabel
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getFieldTemplate();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return Module::getModulePath() . 'warning.twig';
    }

    protected function getWarningLabel()
    {
        return static::t('SkinActYotpoReviews warning message');
    }
}
