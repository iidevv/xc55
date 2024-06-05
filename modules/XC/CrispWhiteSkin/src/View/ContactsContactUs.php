<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View;

use XCart\Extender\Mapping\Extender;

/**
 * Contact Us
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\ContactUs")
 */
class ContactsContactUs extends \XC\CrispWhiteSkin\View\Contacts
{
    public function getEmail()
    {
        return \XLite\Core\Config::getInstance()->CDev->ContactUs->showEmail
            ? parent::getEmail()
            : '';
    }
}
