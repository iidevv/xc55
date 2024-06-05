<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActFreeGifts\View\Product\AttributeValue\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    protected function getAbsoluteModifierValue(\XLite\Model\AttributeValue\Multiple $value, $field)
    {
        return ($this->isGiftMode() && $field === 'Price')
            ? 0
            : parent::getAbsoluteModifierValue($value, $field);
    }

    protected function isGiftMode()
    {
        return Request::getInstance()->isAJAX()
            && Request::getInstance()->isFromGiftSource();
    }
}
