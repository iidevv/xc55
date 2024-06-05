<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View;

class ThemeTweakerTemplates extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        return array_merge(parent::getAllowedTargets(), ['theme_tweaker_templates']);
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/ThemeTweaker/theme_tweaker_templates/body.twig';
    }

    /**
     * Check - search box is visible or not
     *
     * @return bool
     */
    protected function isSearchVisible()
    {
        return 0 < \XLite\Core\Database::getRepo('XC\ThemeTweaker\Model\Template')->count();
    }
}
