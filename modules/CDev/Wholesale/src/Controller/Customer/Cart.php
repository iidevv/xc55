<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    /**
     * Show message about wrong product amount
     *
     * @param \XLite\Model\OrderItem $item Order item
     *
     * @return void
     */
    protected function processInvalidAmountError(\XLite\Model\OrderItem $item)
    {
        if ($item->hasWrongMinQuantity()) {
            \XLite\Core\TopMessage::addWarning(
                'The minimum amount of "{{product}}" product {{description}} allowed to purchase is {{min}} item(s). Please adjust the product quantity.',
                [
                    'product'     => $item->getProduct()->getName(),
                    'description' => $item->getExtendedDescription(),
                    'min'         => $item->getMinQuantity()
                ]
            );
        } else {
            parent::processInvalidAmountError($item);
        }
    }
}
