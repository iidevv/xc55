<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use HTMLPurifier_Config;

/**
 * HTML to AMP converter based on HTML Purifier
 */
class SimpleHtmlToAmpConverter extends HtmlToAmpConverter
{
    protected $config;

    protected $purifier;

    protected function __construct()
    {
        parent::__construct();

        $this->config = HTMLPurifier_Config::createDefault();

        // Purify HTML according to https://www.ampproject.org/docs/reference/spec

        $this->config->set('CSS.AllowedProperties', []);
        $this->config->set('HTML.ForbiddenElements', [
            'img',
            'script',
            'video',
            'audio',
            'iframe',
            'frame',
            'frameset',
            'object',
            'param',
            'applet',
            'embed',
            'style',
        ]);

        $this->purifier = new \HTMLPurifier($this->config);
    }

    public function convert($html)
    {
        return $this->purifier->purify($html);
    }
}
