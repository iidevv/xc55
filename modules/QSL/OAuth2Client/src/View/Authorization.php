<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View;

use XCart\Extender\Mapping\Extender;

/**
 * Authorization
 * @Extender\Mixin
 */
class Authorization extends \XLite\View\Authorization
{
    /**
     * Is checkout authorization box or not
     *
     * @return boolean
     */
    public function isCheckoutAuthBox()
    {
        return \XLite\Core\Request::getInstance()->target == 'checkout'
            && !\XLite\Core\Request::getInstance()->isAJAX();
    }
}
