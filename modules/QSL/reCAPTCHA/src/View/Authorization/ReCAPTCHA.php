<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\View\Authorization;

use XCart\Extender\Mapping\ListChild;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * reCAPTCHA field on authorization forms.
 *
 * @ListChild (list="customer.signin.fields", weight="250", zone="customer")
 * @ListChild (list="customer.signin.popup.fields", weight="250", zone="customer")
 * @ListChild (list="checkout.signin.form", weight="25", zone="customer")
 * @ListChild (list="recover.password.fields", weight="250", zone="customer")
 */
class ReCAPTCHA extends \XLite\View\AView
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/reCAPTCHA/authorization/parts/reCAPTCHA.twig';
    }

    /**
     * Whether the "star" column is visible, or not.
     *
     * @return bool
     */
    protected function isStarColumnVisible()
    {
        return in_array($this->getViewListName(), $this->getStarColumnLists());
    }

    /**
     * Names of lists which require the "star" column in the field markup.
     *
     * @return array
     */
    protected function getStarColumnLists()
    {
        return [
            'recover.password.fields',
        ];
    }

    /**
     * Returns the name of the view list in which this widget is being displayed.
     *
     * @return string
     */
    protected function getViewListName()
    {
        return $this->viewListName;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible() && $this->isRecaptchaEnabledForm();
    }

    /**
     * Check if the reCAPTCHA field is required for the form being displayed.
     *
     * The verification happens in controllers responsible for the forms:
     * - \XLite\Controller\Customer\Login
     * - \XLite\Controller\Customer\RecoverPassword
     *
     * @return bool
     */
    protected function isRecaptchaEnabledForm()
    {
        $validator = Validator::getInstance();

        return ($this->isLoginForm() && $validator->isRequiredForLoginForm())
            || ($this->isRecoveryForm() && $validator->isRequiredForRecoveryForm());
    }

    /**
     * Check if it is the login form.
     *
     * @return bool
     */
    protected function isLoginForm()
    {
        return is_numeric(strpos($this->getViewListName(), 'signin'));
    }

    /**
     * Check if it is the password recovery form.
     *
     * @return bool
     */
    protected function isRecoveryForm()
    {
        return is_numeric(strpos($this->getViewListName(), 'recover'));
    }

    protected function isLabelVisible(): bool
    {
        return true;
    }
}
