<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActContactUsPage\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Contact us controller
 *
 * @Extender\Mixin
 */
class ContactUs extends \CDev\ContactUs\Controller\Customer\ContactUs
{
    /**
     * Return the current page title (for the content area)
     *
     * @return string
     */
    public function getTitle()
    {
        return static::t('SkinActContactUsPage Request a Catalog & Contact Us');
    }
}