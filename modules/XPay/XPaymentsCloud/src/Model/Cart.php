<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XPay\XPaymentsCloud\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Cart
 *
 * @Extender\Mixin
 */
abstract class Cart extends \XLite\Model\Cart implements \XLite\Base\IDecorator
{
    /**
     * Returns the list of session vars that must be cleared on logoff
     *
     * @return array
     */
    public function getSessionVarsToClearOnLogoff()
    {
        return parent::getSessionVarsToClearOnLogoff() + array(
            'buy_with_wallet_order_id'
        );
    }
}
