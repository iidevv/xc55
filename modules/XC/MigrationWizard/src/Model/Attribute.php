<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Decorated attribute model.
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 *
 * @see can be removed after the fix https://bt.x-cart.com/view.php?id=50973
 *
 * @Extender\Mixin
 * @Extender\After ("QSL\ShopByBrand")
 */
class Attribute extends \XLite\Model\Attribute
{
    /**
     * Check If The Attribute Is The One That Stores Product Brands.Can Be Removed After The Fix https://bt.x-cart.com/view.php?id=50973
     *
     * @return boolean
     */
    public function isBrandAttribute()
    {
        if ($this->getId()) {
            return parent::isBrandAttribute();
        } else {
            return false;
        }
    }
}
