<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Get theme files directory
     *
     * @return string
     */
    public static function getThemeDir()
    {
        return LC_DIR_PUBLIC . 'var/theme/';
    }

    /**
     * Get URL for resource by path
     *
     * @return string
     */
    public static function getResourceURL($path)
    {
        $webRootPrefix = \Includes\Utils\ConfigParser::getOptions(['host_details', 'public_dir']) ? 'public/' : '';

        return \XLite::getInstance()->getShopURL($webRootPrefix . substr($path, strlen(LC_DIR_PUBLIC)));
    }
}
