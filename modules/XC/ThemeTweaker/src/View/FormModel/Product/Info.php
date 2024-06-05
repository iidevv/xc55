<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ThemeTweaker\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product form model
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * @return string
     */
    protected function getProductPreviewURL()
    {
        return parent::getProductPreviewURL() . '&activate_editor=1';
    }
}
