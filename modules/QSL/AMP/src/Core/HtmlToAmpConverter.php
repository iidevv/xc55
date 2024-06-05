<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

/**
 * HTML to AMP converter abstract factory
 */
abstract class HtmlToAmpConverter
{
    protected static $instance;

    protected function __construct()
    {
    }

    /**
     * Get converter instance suitable for the current environment
     *
     * @return HtmlToAmpConverter
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            // Lullabot/amp-library requires PHP >= 5.5.0

            self::$instance = version_compare(PHP_VERSION, '5.5.0') >= 0
                ? new AdvancedHtmlToAmpConverter()
                : new SimpleHtmlToAmpConverter();
        }

        return self::$instance;
    }

    abstract public function convert($html);
}
