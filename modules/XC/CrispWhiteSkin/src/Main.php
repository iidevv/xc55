<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin;

use Includes\Utils\Module\Manager;
use XLite\Core\Layout;

abstract class Main extends \XLite\Module\AModuleSkin
{
    /**
     * Check if skin is based on Crisp White theme
     *
     * @return boolean
     */
    public static function isCrispWhiteBasedSkin()
    {
        return true;
    }

    /**
     * Returns supported layout types
     *
     * @return array
     */
    public static function getLayoutTypes()
    {
        return [
            Layout::LAYOUT_GROUP_DEFAULT => Layout::getInstance()->getLayoutTypes(),
            Layout::LAYOUT_GROUP_HOME    => Layout::getInstance()->getLayoutTypes(),
        ];
    }

    /**
     * Returns image sizes
     *
     * @return array
     */
    public static function getImageSizes()
    {
        return [
            \XLite\Logic\ImageResize\Generator::MODEL_PRODUCT => [
                'SBSmallThumbnail' => [120, 120],
                'XSThumbnail'      => [60, 60],
                'MSThumbnail'      => [60, 60],
            ],
        ];
    }

    /**
     * Determines if some module is enabled
     *
     * @return boolean
     */
    public static function isModuleEnabled($name)
    {
        [$author, $name] = explode('\\', $name);

        return Manager::getRegistry()->isModuleEnabled($author, $name);
    }

    /**
     * Check if skin supports cloud zoom
     *
     * @return boolean
     */
    public static function isUseCloudZoom()
    {
        return true;
    }

    /**
     * Check if image lazy loading is supported by skin
     *
     * @return boolean
     */
    public static function isUseLazyLoad()
    {
        return true;
    }
}
