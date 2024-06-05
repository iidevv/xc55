<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\URLManager;

/**
 * @Extender\Mixin
 */
class Layout extends \XLite\Core\Layout
{
    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo);

        return $url ?: parent::getLogo();
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getMobileLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->mobileLogo);

        if (
            empty($url)
            && \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo
        ) {
            // use customer's defined desktoplogo as mobilelogo if mobilelogo is not set
            $url = $this->getLogo();
        }

        return $url ?: parent::getMobileLogo();
    }

    /**
     * Get logo alt
     *
     * @return string
     */
    public function getLogoAlt()
    {
        return \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo_alt
            ?: parent::getLogoAlt();
    }

    /**
     * Get logo to invoice
     *
     * @return string
     */
    public function getInvoiceLogo()
    {
        $partUrl = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->logo);

        if (!$partUrl) {
            return parent::getInvoiceLogo();
        }

        $imageSizes = \XLite\Logic\ImageResize\Generator::defineImageSizes();
        $invoiceLogoSizes = $imageSizes['XLite\Model\Image\Common\Logo']['Invoice'];

        $url = "var/images/logo/" . implode('.', $invoiceLogoSizes) . '/' . $partUrl;
        $path = LC_DIR_ROOT . $url;

        if (!file_exists($path)) {
            return parent::getInvoiceLogo();
        }

        switch ($this->interface) {
            case \XLite::INTERFACE_MAIL:
            case \XLite::INTERFACE_PDF:
                return $url;

            default:
                return URLManager::getShopURL(
                    $url,
                    null,
                    [],
                    URLManager::URL_OUTPUT_SHORT
                );
        }
    }

    /**
     * Return favicon resource path
     *
     * @return string
     */
    public function getFavicon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->favicon);

        $publicDir = 'public/';
        if (substr($url, 0, strlen($publicDir)) === $publicDir) {
            $url = substr($url, strlen($publicDir));
        }

        return $url ?: parent::getFavicon();
    }

    /**
     * Get apple icon
     *
     * @return string
     */
    public function getAppleIcon()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->appleIcon);

        $publicDir = 'public/';
        if (substr($url, 0, strlen($publicDir)) === $publicDir) {
            $url = substr($url, strlen($publicDir));
        }

        return $url ?: parent::getAppleIcon();
    }
}
