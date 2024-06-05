<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\USPS;

use CDev\USPS\Model\Shipping\PBAPI\RequestFactory;

abstract class Main extends \XLite\Module\AModule
{
    /**
     * Return link to settings form
     *
     * @return string
     */
    public static function getSettingsForm()
    {
        return \XLite\Core\Converter::buildURL('usps');
    }

    /**
     * Return true if module should work in strict mode
     * (strict mode enables the logging of errors like 'The module is not configured')
     *
     * @return boolean
     */
    public static function isStrictMode()
    {
        return false;
    }

    /**
     * @param \XLite\Core\ConfigCell|null $config
     *
     * @return RequestFactory
     */
    public static function getRequestFactory($config = null)
    {
        if (is_null($config)) {
            $config = \XLite\Core\Config::getInstance()->CDev->USPS;
        }

        return new RequestFactory(
            $config->pbSandbox
                ? RequestFactory::MODE_SANDBOX
                : RequestFactory::MODE_PRODUCTION
        );
    }

    /**
     * @return string
     */
    public static function getUrlLive(): string
    {
        return 'https://production.shippingapis.com/ShippingAPI.dll';
    }

    /**
     * @return string
     */
    public static function getUrlTest(): string
    {
        return 'https://stg-secure.shippingapis.com/ShippingApi.dll';
    }
}
