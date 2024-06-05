<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Product\Details\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Product attributes
 *
 * @Extender\Mixin
 */
class Attributes extends \XLite\View\Product\Details\Admin\Attributes
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/product/attributes/style.css';

        return $list;
    }
}
