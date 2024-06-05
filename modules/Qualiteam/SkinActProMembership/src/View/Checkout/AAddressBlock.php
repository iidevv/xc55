<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\Checkout;

use XCart\Extender\Mapping\Extender;

/**
 * The "product" model class
 * @Extender\Mixin
 */
abstract class AAddressBlock extends \XLite\View\Checkout\AAddressBlock
{
    protected function isCreateProfile()
    {
        return true;
    }
}