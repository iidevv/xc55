<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActMain\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\URLManager;


/**
 * @Extender\Mixin
 * @Extender\After("CDev\SimpleCMS")
 *
 */
class Layout extends \XLite\Core\Layout
{
    public function getInvoiceLogoPdf()
    {
        $partUrl = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->pdfLogo);

        if ($partUrl) {

            $path = LC_DIR_PUBLIC . $partUrl;

            if (file_exists($path)) {
                return 'public/' . $partUrl;
            }
        }

        return null;
    }

    public function getInvoiceLogoMail()
    {
        $partUrl = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->mailLogo);

        if ($partUrl) {

            $path = LC_DIR_PUBLIC . $partUrl;

            if (file_exists($path)) {
                return 'public/' . $partUrl;
            }
        }

        return null;
    }

    public function getInvoiceLogoWeb()
    {
        $partUrl = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->mailLogo);

        if ($partUrl) {

            $path = LC_DIR_PUBLIC . $partUrl;

            if (file_exists($path)) {
                return URLManager::getShopURL(
                    $partUrl,
                    null,
                    [],
                    URLManager::URL_OUTPUT_SHORT
                );
            }
        }

        return null;
    }

    public function getInvoiceLogo()
    {
        $result = match ($this->interface) {
            \XLite::INTERFACE_PDF => $this->getInvoiceLogoPdf(),
            \XLite::INTERFACE_MAIL => $this->getInvoiceLogoMail(),
            \XLite::INTERFACE_WEB => $this->getInvoiceLogoWeb(),
            default => null,
        };

        return $result ?? parent::getInvoiceLogo();
    }

    public function getMailLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->mailLogo);

        $publicDir = 'public/';
        if (substr($url, 0, strlen($publicDir)) === $publicDir) {
            $url = substr($url, strlen($publicDir));
        }

        return $url ?: parent::getInvoiceLogo();
    }

    public function getPdfLogo()
    {
        $url = str_replace(LC_DS, '/', \XLite\Core\Config::getInstance()->CDev->SimpleCMS->pdfLogo);

        $publicDir = 'public/';
        if (substr($url, 0, strlen($publicDir)) === $publicDir) {
            $url = substr($url, strlen($publicDir));
        }

        return $url ?: parent::getInvoiceLogo();
    }
}
