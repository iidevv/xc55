<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActOrderMessaging\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;


/**
 * @Extender\Mixin
 */
class Conversation extends \XC\VendorMessages\Controller\Customer\Conversation
{
    protected function createNewMessage()
    {
        $message = parent::createNewMessage();
        $files = Request::getInstance()->message_image;
        $files = is_array($files) ? $files : [];
        $message->processFiles('images', $files);
        return $message;
    }
}