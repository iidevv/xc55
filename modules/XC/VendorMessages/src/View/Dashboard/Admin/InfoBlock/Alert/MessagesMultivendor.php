<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Alert;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;
use XLite\Core\Database;
use XC\VendorMessages\Main as VendorMessagesMain;
use XC\VendorMessages\Model\Message;

/**
 * @Extender\Mixin
 * @Extender\After ("XC\VendorMessages")
 * @Extender\Depend ("XC\MultiVendor")
 */
class MessagesMultivendor extends \XC\VendorMessages\View\Dashboard\Admin\InfoBlock\Alert\Messages
{
    /**
     * @return int
     */
    protected function getCounter()
    {
        return Auth::getInstance()->isVendor()
            ? Database::getRepo(Message::class)->countUnreadForVendor()
            : Database::getRepo(Message::class)->countUnreadForAdmin();
    }

    /**
     * @return bool
     */
    protected function checkACL()
    {
        return parent::checkACL()
            || (Auth::getInstance()->isVendor() && VendorMessagesMain::isVendorAllowedToCommunicate());
    }
}
