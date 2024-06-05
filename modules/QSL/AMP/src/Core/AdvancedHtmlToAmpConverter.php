<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use Includes\Utils\URLManager;
use XLite\Core\Cache\ExecuteCached;
use XLite\Core\Request;
use QSL\AMP\Core\Lullabot\AMP\AMPFactory;

/**
 * HTML to AMP converter based on Lullabot/amp-library
 */
class AdvancedHtmlToAmpConverter extends HtmlToAmpConverter
{
    public function convert($html)
    {
        $htmlHash = md5($html);

        return ExecuteCached::executeCached(static function () use ($html) {
            $baseUrl = URLManager::getShopURL('');

            $options = [
                'img_max_fixed_layout_width' => 320,
                'base_url_for_relative_path' => $baseUrl,
                // Fix for Lullabot AMP incorrect HTTPS detection:
                'request_scheme' => Request::getInstance()->isHTTPS() ? 'https://' : 'http://',
            ];

            $amp = AMPFactory::createInstance();

            $amp->loadHtml($html, $options);

            return $amp->convertToAmpHtml();
        }, ['AdvancedHtmlToAmpConverter::convert', $htmlHash]);
    }
}
