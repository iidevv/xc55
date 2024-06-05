<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\FormField\Textarea;

use XCart\Extender\Mapping\Extender;

/**
 * FroalaAdvanced
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\FroalaEditor")
 */
class FroalaDescription extends \XC\CustomProductTabs\View\FormField\Textarea\Description
{
    /**
     * Returns current theme style files
     *
     * @return array
     */
    protected function getThemeStyles()
    {
        $files = parent::getThemeStyles();

        $path = \XLite\Core\Layout::getInstance()->getResourceWebPath(
            'modules/XC/CustomProductTabs/froala/description.css',
            \XLite\Core\Layout::WEB_PATH_OUTPUT_URL,
            \XLite::INTERFACE_WEB,
            \XLite::ZONE_CUSTOMER
        );

        if ($path) {
            $files[] = $this->getShopURL($path, null, ['t' => LC_START_TIME]);
        }

        return $files;
    }
}
