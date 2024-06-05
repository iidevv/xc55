<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Login extends \XLite\Controller\Customer\Login
{
    protected function doActionLogin()
    {
        parent::doActionLogin();

        $this->movePostponedToRealWishlist();
    }
}
