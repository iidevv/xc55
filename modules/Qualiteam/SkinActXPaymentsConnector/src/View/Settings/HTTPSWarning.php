<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\View\Settings;

use Qualiteam\SkinActXPaymentsConnector\Core\Settings;
use XLite\Core\Config;

/**
 * Warning for the disabled HTTPS
 */
class HTTPSWarning extends ASettings
{
    /**
     * Check if HTTPS options are enabled
     *
     * @return boolean
     */
    protected function isEnabledHTTPS()
    {
        return Config::getInstance()->Security->admin_security
            && Config::getInstance()->Security->customer_security;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . '/https_warning.twig';
    }

    /**
     * List of tabs/pages where this setting should be displayed
     *
     * @return array
     */
    public function getPages()
    {
        return array_keys(Settings::getAllPages());
    }
}
