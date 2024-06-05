<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\View\Form\Product\Modify;

use XCart\Extender\Mapping\Extender;

/**
 * Details
 *
 * @Extender\Mixin
 */
abstract class Single extends \XLite\View\Form\Product\Modify\Single
{
    /**
     * Get js files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/Qualiteam/SkinActXPaymentsSubscriptions/js/product_modify_single.js';

        return $list;
    }
}
