<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActFreeGifts\View\Form\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class AddToCart extends \XLite\View\Form\Product\AddToCart
{
    protected function getFormDefaultParams()
    {
        $list = parent::getFormDefaultParams();

        if (Request::getInstance()->isFromGiftSource()) {
            $list['_source'] = Request::getInstance()->_source;
            $list['action'] = 'add_items';
        }

        return $list;
    }
}
