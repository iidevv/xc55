<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActFreeGifts\View\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 * @Extender\After("XC\ProductVariants")
 */
class QuantityBox extends \XLite\View\Product\QuantityBox
{
    protected function getAdditionalValidate()
    {
        if ($this->isGiftMode()) {
            return ',max[1]';
        }

        return parent::getAdditionalValidate();
    }

    protected function isGiftMode():bool
    {
        return Request::getInstance()->isAJAX()
            && Request::getInstance()->isFromGiftSource();
    }
}
