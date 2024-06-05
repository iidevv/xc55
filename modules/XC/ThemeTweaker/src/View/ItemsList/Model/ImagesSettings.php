<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Images settings items list widget
 * @Extender\Mixin
 */
class ImagesSettings extends \XLite\View\ItemsList\Model\ImagesSettings
{
    /**
     * Define request data
     *
     * @return array
     */
    protected function defineRequestData()
    {
        $params = parent::defineRequestData();
        if (isset($params['new'])) {
            unset($params['new']);
        }

        return $params;
    }
}
