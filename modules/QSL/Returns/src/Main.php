<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns;

/**
 * Main module
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('return_reasons');
    }

    /**
     * Check if "Actions" functionality enabled or not
     *
     * @return boolean
     */
    public static function isActionsEnabled()
    {
        return (bool)\XLite\Core\Config::getInstance()->QSL->Returns->enable_actions;
    }
}
