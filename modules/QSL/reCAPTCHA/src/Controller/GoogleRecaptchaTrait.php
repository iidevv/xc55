<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Controller;

use XLite\InjectLoggerTrait;
use QSL\reCAPTCHA\View\FormField\ReCAPTCHA as Field;

/**
 * Provides methods to perform Google reCAPTCHA verification in controllers.
 */
trait GoogleRecaptchaTrait
{
    use InjectLoggerTrait;

    /**
     * Checks if Google reCAPTCHA validation is required.
     *
     * @return bool
     */
    protected function isGoogleRecaptchaRequired()
    {
        return false;
    }

    protected function verifyGoogleRecaptcha()
    {
        $field = new Field();
        $field->setValue($this->getGoogleRecaptchaResponse());
        [$valid, $error] = $field->validate();

        if (!$valid) {
            $this->showGoogleRecaptchaError($error);
        }

        return $valid;
    }

    /**
     * Returns the challenge response value added by the Google reCAPTCHA widget.
     *
     * @return string
     */
    protected function getGoogleRecaptchaResponse()
    {
        return \XLite\Core\Request::getInstance()->{Field::POST_PARAM_NAME};
    }

    /**
     * Displays the Google reCAPTCHA error message on the page.
     *
     * @param string $error Error message
     *
     * @return void
     */
    protected function showGoogleRecaptchaError($error)
    {
        \XLite\Core\TopMessage::addError($error);
    }
}
