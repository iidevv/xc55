<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\reCAPTCHA\Module\XC\NewsletterSubscriptions\View\Form;

use XCart\Extender\Mapping\Extender;
use QSL\reCAPTCHA\Logic\reCAPTCHA\Validator;

/**
 * Newsletter subscription form
 * @Extender\Mixin
 */
class Subscription extends \XC\NewsletterSubscriptions\View\Form\Subscription
{
    protected function getEndTemplate()
    {
        if (Validator::getInstance()->isRequiredForNewsletterSubscriptions()) {
            return 'modules/QSL/reCAPTCHA/newsletter_subscriptions/form_end.twig';
        }

        return parent::getEndTemplate();
    }
}
