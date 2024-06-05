<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\View;

use XCart\Extender\Mapping\Extender;
use CDev\GoogleAnalytics\Core\GA;

/**
 * @Extender\Mixin
 *
 * Abstract widget
 */
abstract class Search extends \XLite\View\Form\Product\Search\Customer\Main
{
    /**
     * Register JS files
     *
     * @return array
     * @noinspection PhpMissingReturnTypeInspection
     * @noinspection ReturnTypeCanBeDeclaredInspection
     */
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), GA::getLibrary()->getJsList()->search);
    }
}
