<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ESelectHPP;

use XCart\Extender\Mapping\Extender;

/**
 * Class represents an order
 * @Extender\Mixin
 */
class XLite extends \XLite
{
    /**
     * Dispatch request
     *
     * @return string
     */
    protected static function dispatchRequest()
    {
        $result = parent::dispatchRequest();
        if (
            strlen(\XLite\Core\Request::getInstance()->response_order_id) > 2
            && isset(\XLite\Core\Request::getInstance()->response_order_id)
            && isset(\XLite\Core\Request::getInstance()->result)
            && isset(\XLite\Core\Request::getInstance()->trans_name)
            && isset(\XLite\Core\Request::getInstance()->cardholder)
            && isset(\XLite\Core\Request::getInstance()->message)
        ) {
            $result = 'payment_return';
        }

        return $result;
    }
}
