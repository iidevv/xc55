<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Model product selector controller
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class ModelProductSelector extends \XLite\Controller\Admin\ModelProductSelector
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        $vendorAccessGranted = \XLite\Core\Auth::getInstance()->isPermissionAllowed('[vendor] manage catalog');

        return parent::checkACL() || $vendorAccessGranted;
    }
}
