<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ContactUs\View\FormField;

/**
 * Captcha
 */
class Captcha extends \XLite\View\FormField\AFormField
{
    /**
     * @inheritdoc
     */
    protected function isVisible()
    {
        return parent::isVisible() && \CDev\ContactUs\Core\ReCaptcha::getInstance()->isConfigured();
    }

    /**
     * @inheritdoc
     */
    public function getFieldType()
    {
        return static::FIELD_TYPE_COMPLEX;
    }

    /**
     * @inheritdoc
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function getFieldTemplate()
    {
        return 'modules/CDev/ContactUs/contact_us/fields/field.captcha.twig';
    }
}
