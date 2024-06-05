<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Model;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
abstract class Multiple extends \XLite\Model\AttributeValue\Multiple
{
    public function getPriceModifier()
    {
        return $this->isGiftMode() ? 0 : parent::getPriceModifier();
    }

    protected function isGiftMode(): bool
    {
        return Request::getInstance()->isAJAX()
            && Request::getInstance()->isFromGiftSource();
    }
}
