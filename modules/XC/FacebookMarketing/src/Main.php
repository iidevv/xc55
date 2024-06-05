<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('facebook_marketing');
    }

    /**
     * Check if Facebook Pixel enabled
     *
     * @return bool
     */
    public static function isPixelEnabled()
    {
        return (bool) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->pixel_id;
    }

    /**
     * Check if Facebook Pixel Advanced Matching enabled
     *
     * @return bool
     */
    public static function isAdvancedMatchingEnabled()
    {
        return static::isPixelEnabled() && (bool) \XLite\Core\Config::getInstance()->XC->FacebookMarketing->advanced_matching;
    }
}
