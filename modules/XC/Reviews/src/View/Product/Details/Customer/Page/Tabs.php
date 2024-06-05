<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Tabs extends \XLite\View\Product\Details\Customer\Page\Tabs
{
    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params['anonymous'] = \XLite\Core\Auth::getInstance()->isLogged();

        return $params;
    }
}
