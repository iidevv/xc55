<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\QSL\MyWishlist\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * User profile page controller
 *
 * @Extender\Mixin
 * @Extender\Depend("QSL\MyWishlist")
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    protected function postprocessActionRegisterSuccessWishlistActions()
    {
        if (!$this->getRequiresActivation()) {
            parent::postprocessActionRegisterSuccessWishlistActions();
        }
    }
}
