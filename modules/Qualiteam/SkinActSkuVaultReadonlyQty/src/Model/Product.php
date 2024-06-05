<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Model;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Hardcoded to true to avoid making this module dependent on Qualiteam\SkinActSkuVault
     *
     * @return bool
     */
    public function isSkippedFromSync()
    {
        return true;
    }
}
