<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XC\CanadaPost\Core\Mail\ProductsReturnApproved;
use XC\CanadaPost\Core\Mail\ProductsReturnRejected;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * Send mail notification to customer that his products return has been approved
     *
     * @param \XC\CanadaPost\Model\ProductsReturn $return Canada Post products return
     *                                                                 model
     */
    public static function sendProductsReturnApproved(\XC\CanadaPost\Model\ProductsReturn $return)
    {
        static::getBus()->dispatch(new SendMail(ProductsReturnApproved::class, [$return]));
    }

    /**
     * Send mail notification to customer that his products return has been rejected
     *
     * @param \XC\CanadaPost\Model\ProductsReturn $return Canada Post products return
     *                                                                 model
     */
    public static function sendProductsReturnRejected(\XC\CanadaPost\Model\ProductsReturn $return)
    {
        static::getBus()->dispatch(new SendMail(ProductsReturnRejected::class, [$return]));
    }
}
