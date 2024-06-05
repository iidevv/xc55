<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Checkout
 * @Extender\Mixin
 */
class Checkout extends \XLite\Controller\Customer\Checkout
{
    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function getSigninTitle()
    {
        return $this->isRegisterMode()
            ? static::t('Create new account')
            : static::t('Sign in');
    }

    /**
     * Define the account links availability
     *
     * @return boolean
     */
    public function isRegisterMode()
    {
        return \XLite\Core\Request::getInstance()->mode === 'register' && !$this->isCheckoutAvailable();
    }
}
