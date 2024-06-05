<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\Core;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class Request extends \XLite\Core\Request
{
    public function isFromGiftSource():bool
    {
        return $this->_source === 'gift';
    }
}
