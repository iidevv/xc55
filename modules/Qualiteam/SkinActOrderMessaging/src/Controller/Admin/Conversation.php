<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActOrderMessaging\Controller\Admin;

use XLite\Core\Request;
use XCart\Extender\Mapping\Extender;

/**
 * Order page controller
 * @Extender\Mixin
 */
class Conversation extends \XC\VendorMessages\Controller\Admin\Conversation
{
    /**
     * @return boolean
     */
    public function isTitleVisible()
    {
        return false;
    }

    protected function createNewMessage()
    {
        $message = parent::createNewMessage();
        $files = Request::getInstance()->message_image;
        $files = is_array($files) ? $files : [];
        $message->processFiles('images', $files);
        return $message;
    }
}