<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BraintreeVZ;

use XLite\Core\Cache\ExecuteCached;

/**
 * PayPal powered by Braintree payment gateway 
 */
abstract class Main extends \XLite\Module\AModule
{
    /**
     * @return Object|\XLite\Model\Payment\Method
     */
    public static function getMethod()
    {
        return ExecuteCached::executeCachedRuntime(function () {
            return \XLite\Core\Database::getRepo('XLite\Model\Payment\Method')
                ->findOneBy(['class' => 'QSL\BraintreeVZ\Model\Payment\Processor\BraintreeVZ']);
        }, [__CLASS__, __FUNCTION__]);
    }

    /**
     * @return \XLite\Model\Payment\Base\Processor
     */
    public static function getProcessor()
    {
        return static::getMethod()->getProcessor();
    }
}
