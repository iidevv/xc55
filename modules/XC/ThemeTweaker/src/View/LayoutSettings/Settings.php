<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\LayoutSettings;

use XCart\Extender\Mapping\Extender;

/**
 * Layout settings
 * @Extender\Mixin
 */
class Settings extends \XLite\View\LayoutSettings\Settings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/XC/ThemeTweaker/layout_settings/style.less';

        return $list;
    }

    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/ThemeTweaker/theme_tweaker_templates/controller.js';
        $list[] = 'modules/XC/ThemeTweaker/layout_settings/layout_mode_btn_controller.js';
        $list[] = 'modules/XC/ThemeTweaker/layout_settings/labels_editor_btn_controller.js';

        return $list;
    }
}
