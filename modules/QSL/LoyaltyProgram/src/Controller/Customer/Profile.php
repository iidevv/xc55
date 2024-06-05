<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;

/**
 * @Extender\Mixin
 */
class Profile extends \XLite\Controller\Customer\Profile
{
    /**
     * "Register" action
     *
     * @return boolean
     */
    protected function doActionRegister()
    {
        $result = parent::doActionRegister();

        if ($result && $this->getModelForm()) {
            // Reward the customer for registering in the store
            LoyaltyProgram::getInstance()->rewardForSignup($this->getModelForm()->getModelObject());

            \XLite\Core\Database::getEM()->flush();
        }

        return $result;
    }
}
