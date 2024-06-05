<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\Model;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;
use XLite\View\FormField\AFormField;

/**
 * Contact Us form.
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ContactUs")
 */
class Contact extends \CDev\ContactUs\View\Model\Contact
{
    /**
     * @inheritdoc
     */
    public function __construct($params = [], $sections = [])
    {
        parent::__construct($params, $sections);

        if (Validator::getInstance()->isRequiredForContactForm()) {
            $this->schemaDefault['recaptcha'] = [
                static::SCHEMA_CLASS      => '\QSL\reCAPTCHA\View\FormField\ReCAPTCHA',
                static::SCHEMA_REQUIRED   => false,
                static::SCHEMA_NAME       => 'google-recaptcha',
                static::SCHEMA_FIELD_ONLY => true,
                AFormField::PARAM_ID      => 'google-recaptcha',
            ];
        } else {
            unset($this->schemaDefault['recaptcha']);
        }
    }

    /**
     * Validate captcha
     */
    protected function validateCaptcha()
    {
        // The reCAPTCHA field validates itself, so we don't need a separate
        // validation method anymore
    }
}
