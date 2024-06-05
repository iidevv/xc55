<?php

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
class Quantity extends \XLite\View\Product\Details\Customer\Quantity
{

    protected function getCSSClass()
    {
        $class = parent::getCSSClass();
        if (Request::getInstance()->isFromGiftSource()) {
            $class .= ' SkinActFreeGifts ';
        }
        return $class;
    }

    public function getCSSFiles()
    {
        $list =parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActFreeGifts/Quantity.css';
        return $list;
    }

}