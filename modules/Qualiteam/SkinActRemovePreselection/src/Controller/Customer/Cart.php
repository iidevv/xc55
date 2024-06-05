<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActRemovePreselection\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Cart extends \XLite\Controller\Customer\Cart
{

    protected function processAddItemError()
    {
        if ($this->getCart()->hasInvalidAttributes) {
            \XLite\Core\TopMessage::addError('SkinActRemovePreselection Please select product options');
            return;
        }

        parent::processAddItemError();
    }
}