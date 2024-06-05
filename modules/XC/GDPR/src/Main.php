<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR;

use Includes\Utils\Module\Module;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Check if current skin is crisp white based
     *
     * @return bool
     */
    public static function isCrispWhiteBasedSkinEnabled()
    {
        $module    = \XLite\Core\Skin::getInstance()->getCurrentSkinModule();
        $mainClass = Module::getMainClassName($module['id']);

        if (
            $module
            && method_exists($mainClass, 'isCrispWhiteBasedSkin')
        ) {
            return Module::callMainClassMethod($module['id'], 'isCrispWhiteBasedSkin');
        }

        if (
            $module
            && \XLite\Core\Layout::getInstance()->getResourceFullPath(
                'css/less/mmenu-fixes.less',
                \XLite::INTERFACE_WEB,
                \XLite::ZONE_CUSTOMER
            )
        ) {
            return true;
        }

        return false;
    }
}
