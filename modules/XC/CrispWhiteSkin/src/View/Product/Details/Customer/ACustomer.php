<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ACustomer extends \XLite\View\Product\Details\Customer\ACustomer
{
    public function getJSFiles()
    {
        return array_merge(parent::getJSFiles(), [
            'product/details/parts/script.js'
        ]);
    }
}
