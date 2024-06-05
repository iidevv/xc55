<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Messages
 *
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class MessagesMultivendor extends \XC\VendorMessages\Controller\Admin\Messages
{
    /**
     * @inheritdoc
     */
    public function checkAccess()
    {
        return parent::checkAccess()
            && (!\XLite\Core\Auth::getInstance()->isVendor() || \XC\VendorMessages\Main::isVendorAllowedToCommunicate());
    }

    /**
     * @inheritdoc
     */
    public function checkACL()
    {
        return parent::checkACL()
            || \XLite\Core\Auth::getInstance()->isPermissionAllowed('[vendor] manage orders');
    }

    /**
     * @inheritdoc
     */
    public function isSearchVisible()
    {
        return (parent::isSearchVisible() && !\XLite\Core\Auth::getInstance()->isVendor())
            || (\XLite\Core\Auth::getInstance()->isVendor() && \XLite\Core\Database::getRepo('XC\VendorMessages\Model\Message')->countByVendor() > 0);
    }
}
