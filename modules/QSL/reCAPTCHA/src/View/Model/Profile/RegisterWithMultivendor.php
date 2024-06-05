<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\Model\Profile;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;
use XLite\View\FormField\AFormField;

/**
 * Profile form.
 *
 * @Extender\Mixin
 * @Extender\Depend ({"QSL\reCAPTCHA", "XC\MultiVendor"})
 */
class RegisterWithMultivendor extends \XC\MultiVendor\View\Model\Profile\Register
{
    /**
     * @return array
     */
    protected function defineSchemaMain()
    {
        $result = parent::defineSchemaMain();

        if ($this->isRecaptchaEnabledForm()) {
            $result['google-recaptcha'] = [
                static::SCHEMA_CLASS      => '\QSL\reCAPTCHA\View\FormField\ReCAPTCHA',
                static::SCHEMA_REQUIRED   => false,
                static::SCHEMA_FIELD_ONLY => true,
                AFormField::PARAM_ID      => 'google-recaptcha',
            ];
        }

        return $result;
    }

    /**
     * Check if Google reCAPTCHA is enabled on this page.
     *
     * @return bool
     */
    protected function isRecaptchaEnabledForm()
    {
        return (\XLite::getController()->getTarget() === 'register_vendor')
            &&
            Validator::getInstance()->isRequiredForVendorForm();
    }
}
