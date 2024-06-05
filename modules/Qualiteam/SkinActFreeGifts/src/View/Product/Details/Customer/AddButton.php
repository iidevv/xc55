<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Product\Details\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class AddButton extends \XLite\View\Product\Details\Customer\AddButton
{
    protected function getAddButtonLabel()
    {
        return Request::getInstance()->isFromGiftSource()
            ? 'SkinActFreeGifts Add to cart as a free gift'
            : parent::getAddButtonLabel();
    }
}
