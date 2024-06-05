<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Controller\Customer;

use Qualiteam\SkinActFreeGifts\Core\AddGiftValidator;
use Qualiteam\SkinActMain\Core\IAddProductValidator;
use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XLite\Model\OrderItem;

/**
 * Cart model
 *
 * @Extender\Mixin
 * @Extender\After ("Qualiteam\SkinActMain")
 */
class Cart extends \XLite\Controller\Customer\Cart
{
    protected function getValidator(): IAddProductValidator
    {
        return Request::getInstance()->isFromGiftSource()
            ? new AddGiftValidator()
            : parent::getValidator();
    }

    protected function getPreparedOrderItem(): ?OrderItem
    {
        $item = parent::getPreparedOrderItem();

        if (Request::getInstance()->isFromGiftSource()) {
            $item->setFreeGift(true);
        }

        return $item;
    }
}
