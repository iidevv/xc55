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
class OrderMessages extends \XC\VendorMessages\Controller\Customer\OrderMessages
{
    protected function createNewMessage()
    {
        $message = parent::createNewMessage();
        $message->processFiles('images', Request::getInstance()->message_image);
        return $message;
    }

}