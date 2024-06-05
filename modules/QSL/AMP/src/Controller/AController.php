<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Controller;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Core\URLManager;
use QSL\AMP\Core\AMPDetectorTrait;

/**
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    use AMPDetectorTrait;

    /**
     * Alias: check for an AJAX request
     *
     * @return boolean
     */
    public function isAJAX()
    {
        if (parent::isAJAX()) {
            return true;
        }

        return Request::getInstance()->isAmpXhr();
    }

    /**
     * Replace main template for mobile demo
     *
     * @return string
     */
    protected function getViewerTemplate()
    {
        return static::isAMP() ? 'modules/QSL/AMP/main.twig' : parent::getViewerTemplate();
    }

    /**
     * Run controller
     *
     * @return void
     */
    protected function run()
    {
        $request = Request::getInstance();

        if ($request->isAmpXhr()) {
            // Ensure security of the request
            // https://www.ampproject.org/docs/guides/amp-cors-requests#ensuring-secure-requests

            $corsTrusted = isset($_SERVER['HTTP_ORIGIN'])
                           && $this->isAmpOriginTrusted($_SERVER['HTTP_ORIGIN'], $request->__amp_source_origin);

            $sameOriginTrusted = !isset($_SERVER['HTTP_ORIGIN']) && $this->isAmpSameOrigin();

            if (!$corsTrusted && !$sameOriginTrusted) {
                $this->headerStatus(403);

                die;
            }

            header('AMP-Access-Control-Allow-Source-Origin: ' . $request->__amp_source_origin);
        }

        parent::run();
    }

    /**
     * Check if both origin and source origin are valid. Origin must match any of
     *
     * - *.ampproject.org
     * - *.amp.cloudflare.com
     * - or the publisher's origin (aka yours)
     *
     * Source origin must be publisher's origin
     *
     * @param $origin
     * @param $sourceOrigin
     *
     * @return bool
     */
    protected function isAmpOriginTrusted($origin, $sourceOrigin)
    {
        $originDomain       = preg_replace('#^https?://#', '', $origin);
        $sourceOriginDomain = preg_replace('#^https?://#', '', $sourceOrigin);

        $ampProject    = '.ampproject.org';
        $ampCloudFlare = '.amp.cloudflare.com';

        $ownDomains = array_filter(URLManager::getShopDomains());

        if (
            substr($originDomain, -strlen($ampProject)) !== $ampProject
            && substr($originDomain, -strlen($ampCloudFlare)) !== $ampCloudFlare
            && !in_array($originDomain, $ownDomains)
        ) {
            return false;
        }

        return in_array($sourceOriginDomain, $ownDomains);
    }

    /**
     * Check if request has AMP-Same-Origin: true header
     *
     * @return bool
     */
    protected function isAmpSameOrigin()
    {
        return isset($_SERVER['HTTP_AMP_SAME_ORIGIN']) && $_SERVER['HTTP_AMP_SAME_ORIGIN'] === 'true';
    }
}
