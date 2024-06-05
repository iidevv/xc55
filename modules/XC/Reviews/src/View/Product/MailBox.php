<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class MailBox extends \XLite\View\Product\MailBox
{
    /**
     * @return bool
     */
    protected function isDisplayAddReviewButton()
    {
        return false;
    }
}
