<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\Module\XC\NewsletterSubscriptions\Controller\Customer;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class NewsletterSubscriptions extends \XC\NewsletterSubscriptions\Controller\Customer\NewsletterSubscriptions
{
    /**
     * Subscribe action handler
     */
    protected function doActionSubscribe()
    {
        parent::doActionSubscribe();

        if (Request::getInstance()->isAmpXhr()) {
            // amp-form requires valid JSON in a response
            $this->set('silent', true);
            $this->printAJAX([]);
        }
    }
}
