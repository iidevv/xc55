<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductQuestions\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * REST services end-point
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiVendor")
 */
class Rest extends \XLite\Controller\Admin\Rest
{
    /**
     * Check ACL permissions
     *
     * @return boolean
     */
    public function checkACL()
    {
        return parent::checkACL() || $this->isVendorAllowedRequest();
    }

    /**
     * Check if the request is allowed for the vendor.
     *
     * @return boolean
     */
    protected function isVendorAllowedRequest()
    {
        $action = \XLite\Core\Request::getInstance()->action;
        $name = \XLite\Core\Request::getInstance()->name;
        $auth = \XLite\Core\Auth::getInstance();

        return $auth->isVendor()
            && $auth->isPermissionAllowed('[vendor] manage catalog')
            && (
                ($action === 'get') && ($name === 'translation')
            );
    }
}
