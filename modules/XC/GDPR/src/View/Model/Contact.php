<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Contact
 *
 * @Extender\Mixin
 * @Extender\Depend("CDev\ContactUs")
 */
class Contact extends \CDev\ContactUs\View\Model\Contact
{
    /**
     * @inheritdoc
     */
    public function __construct($params = [], $sections = [])
    {
        parent::__construct($params, $sections);

        $recaptcha = $this->schemaDefault['recaptcha'];
        unset($this->schemaDefault['recaptcha']);

        $this->schemaDefault['gdprConsent'] = [
            self::SCHEMA_CLASS => 'XC\GDPR\View\FormField\Input\GdprConsent',
            self::SCHEMA_LABEL => 'I consent to the collection and processing of my personal data (contact us form)',
        ];

        $this->schemaDefault['recaptcha'] = $recaptcha;
    }

    /**
     * Send email
     */
    protected function sendEmail()
    {
        if (\XLite\Core\Request::getInstance()->gdprConsent && \XLite\Core\Auth::getInstance()->getProfile()) {
            \XLite\Core\Auth::getInstance()->getProfile()->setGdprConsent(true);
        }

        parent::sendEmail();
    }
}
