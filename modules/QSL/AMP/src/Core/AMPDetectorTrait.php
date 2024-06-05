<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Core;

use XLite;
use XLite\Controller\Customer\Category;

/**
 * Methods to detect whether the current page should be displayed in AMP mode
 */
trait AMPDetectorTrait
{
    /**
     * Check if this is an AMP page
     *
     * @return bool
     */
    protected static function isAMP()
    {
        $controller = XLite::getController();

        $isCategory = $controller instanceof Category;

        $request = \XLite\Core\Request::getInstance();

        return $isCategory && isset($request->amp) && $request->amp === '1';
    }

    /**
     * Check if the current page has AMP version
     *
     * @return bool
     */
    protected static function hasAMPVersion()
    {
        $controller = XLite::getController();

        $isCategory = $controller instanceof Category;

        $request = \XLite\Core\Request::getInstance();

        return $isCategory && (!isset($request->amp) || $request->amp !== '1');
    }
}
