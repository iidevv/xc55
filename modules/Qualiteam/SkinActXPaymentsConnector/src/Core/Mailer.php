<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsConnector\Core;

use XCart\Extender\Mapping\Extender;
use XLite;
use XLite\Core\Layout;

/**
 * Mailer
 *
 * @Extender\Mixin
 */
class Mailer extends \XLite\Core\Mailer
{
    /**
     * Check if the email is sent to Admin currently
     *
     * @return bool 
     */
    public static function isMailSendToAdmin()
    {
        return XLite::ADMIN_INTERFACE == Layout::getInstance()->getInnerInterface();
    }
}
