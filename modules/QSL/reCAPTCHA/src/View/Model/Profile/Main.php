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
 * @Extender\Mixin
 */
class Main extends \XLite\View\Model\Profile\Main
{
    public const SCHEMA_FIELD_GOOGLE_RECAPTCHA = 'google-recaptcha';

    /**
     * Return fields list by the corresponding schema
     *
     * @return array
     */
    protected function getFormFieldsForSectionMain()
    {
        if ($this->isRecaptchaEnabledForm()) {
            $this->addGoogleRecaptchaField();
        }

        return parent::getFormFieldsForSectionMain();
    }

    /**
     * Check if Google reCAPTCHA is required for the form being displayed.
     *
     * The verification happens in the class that is responsible for the field:
     * - \QSL\reCAPTCHA\View\FormField\ReCAPTCHA
     *
     * @return bool
     */
    protected function isRecaptchaEnabledForm()
    {
        return (\XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER)
            && Validator::getInstance()->isRequiredForRegistrationForm();
    }

    /**
     * Inject the reCaptcha field into the form.
     *
     * @return void
     */
    protected function addGoogleRecaptchaField()
    {
        $this->mainSchema[self::SCHEMA_FIELD_GOOGLE_RECAPTCHA] = [
            self::SCHEMA_CLASS      => '\QSL\reCAPTCHA\View\FormField\ReCAPTCHA',
            self::SCHEMA_LABEL      => $this->t('Please confirm that you are not a robot'),
            self::SCHEMA_FIELD_ONLY => false,
            self::SCHEMA_REQUIRED   => false,
        ];
    }

    /**
     * Return list of form fields objects by schema
     *
     * @param array $schema Field descriptions
     *
     * @return array
     */
    protected function getFieldsBySchema(array $schema)
    {
        return parent::getFieldsBySchema($this->moveGoogleRecaptchaToBottom($schema));
    }

    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        if (
            Validator::getInstance()->isRequiredForRegistrationForm()
            && isset($this->errorMessages['login'])
        ) {
            \XLite\Core\Event::invalidElement('login', $this->errorMessages['login']);
        }
    }

    /**
     * @return boolean
     */
    protected function checkPassword()
    {
        $result = parent::checkPassword();

        if (
            Validator::getInstance()->isRequiredForRegistrationForm()
            && isset($this->errorMessages['password'])
        ) {
            \XLite\Core\Event::invalidElement('password', $this->errorMessages['password']);
        }

        return $result;
    }

    /**
     * Updates the schema and moves the Google reCAPTCHA field to the bottom.s
     *
     * @param array $schema
     *
     * @return array
     */
    protected function moveGoogleRecaptchaToBottom(array $schema)
    {
        $result = [];

        $recaptcha = false;
        foreach ($schema as $name => $field) {
            if ($name === self::SCHEMA_FIELD_GOOGLE_RECAPTCHA) {
                $recaptcha = $field;
            } else {
                $result[$name] = $field;
            }
        }
        if ($recaptcha) {
            $result[self::SCHEMA_FIELD_GOOGLE_RECAPTCHA] = $recaptcha;
        }

        return $result;
    }
}
