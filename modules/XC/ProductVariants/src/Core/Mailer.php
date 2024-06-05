<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XC\ProductVariants\Core\Mail\LowVariantLimitWarningAdmin;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param array $data Data
     */
    public static function sendLowVariantLimitWarningAdmin(array $data)
    {
        static::getBus()->dispatch(new SendMail(LowVariantLimitWarningAdmin::class, [$data]));
    }
}
