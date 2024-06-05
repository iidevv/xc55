<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoSocial\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Controller extends \XLite\View\Controller
{
    /**
     * Get head prefixes
     *
     * @return array
     */
    public static function defineHTMLPrefixes()
    {
        $list = parent::defineHTMLPrefixes();

        $list['og'] = 'http://ogp.me/ns#';
        $list['fb'] = 'http://ogp.me/ns/fb#';

        if (\XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_namespace) {
            $ns = \XLite\Core\Config::getInstance()->CDev->GoSocial->fb_app_namespace;
            $list[$ns] = 'http://ogp.me/ns/' . $ns . '#';
        }

        return $list;
    }
}
