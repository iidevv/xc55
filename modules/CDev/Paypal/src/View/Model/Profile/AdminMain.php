<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Model\Profile;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("!CDev\SocialLogin")
 */
class AdminMain extends \XLite\View\Model\Profile\AdminMain
{
    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if ($this->getModelObject()->isSocialProfile() && $this->getModelObject()->getSocialLoginProvider() === 'PayPal') {
            unset($this->mainSchema['password'], $this->mainSchema['password_conf']);
        }

        return parent::getFormFieldsForSectionMain();
    }
}
