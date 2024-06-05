<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * Profile form.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\reCAPTCHA", "XC\MultiVendor"})
 */
class MainWithMultivendor extends \XLite\View\Model\Profile\Main
{
    /**
     * Check if Google reCAPTCHA is enabled on this page.
     *
     * @return bool
     */
    protected function isRecaptchaEnabledForm()
    {
        return parent::isRecaptchaEnabledForm()
            || (
                (\XLite::getController()->getTarget() === 'register_vendor')
                &&
                Validator::getInstance()->isRequiredForVendorForm()
            );
    }
}
