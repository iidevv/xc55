<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

/**
 * Provides data for EventTaskProgress widget
 */
trait HttpsCheckerTrait
{
    /**
     * Flags
     */
    private $isAvailableHTTPS;
    private $isValidSSL;

    /**
     * Check if curl is available and we can check availablilty of https
     *
     * @return boolean
     */
    protected function isCurlAvailable()
    {
        return function_exists('curl_init');
    }

    /**
     * Check if HTTPS feature is available and can be enabled
     *
     * @return boolean
     */
    protected function isAvailableHTTPS()
    {
        if ($this->isAvailableHTTPS === null) {
            $this->isAvailableHTTPS = \XLite\Core\URLManager::isSecureURLAccessible($this->getTestURL());
        }

        return $this->isAvailableHTTPS;
    }

    /**
     * Check if SSL certificate is valid
     *
     * @return boolean
     */
    protected function isValidSSL()
    {
        if ($this->isValidSSL === null) {
            $this->isValidSSL = \XLite\Core\URLManager::isSecureURLAccessible($this->getTestURL(), true);
        }

        return $this->isValidSSL;
    }

    /**
     * Get URL to test https connection
     *
     * @return string
     */
    protected function getTestURL()
    {
        $testPath = \XLite\Core\Layout::getInstance()->getResourceWebPath('js/php.js', \XLite\Core\Layout::WEB_PATH_OUTPUT_URL, \XLite::INTERFACE_WEB, \XLite::ZONE_COMMON);

        return \XLite\Core\URLManager::getShopURL($testPath, true);
    }

    /**
     * Get URL to test https connection
     *
     * @return string
     */
    protected function getDomain()
    {
        $url = parse_url($this->getTestURL());

        return $url['host'];
    }

    /**
     * Check if HTTPS options are enabled
     *
     * @return boolean
     */
    protected function isEnabledHTTPS()
    {
        return \XLite\Core\Config::getInstance()->Security->admin_security
            && \XLite\Core\Config::getInstance()->Security->customer_security;
    }
}
