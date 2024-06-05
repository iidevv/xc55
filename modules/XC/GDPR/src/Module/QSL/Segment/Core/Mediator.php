<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\Module\QSL\Segment\Core;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Auth;

/**
 * Event mediator
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\Segment")
 */
class Mediator extends \QSL\Segment\Core\Mediator
{
    /**
     * Check cookies consent
     *
     * @return string
     */
    protected function isConsentRevoked()
    {
        return Auth::getInstance()->isUserDefaultCookiesConsent();
    }

    /**
     * Add message
     *
     * @param string  $type       Message type
     * @param array   $arguments  Message argument
     * @param boolean $ignoreAJAX Ignore AJAX flag
     *
     * @return boolean
     */
    protected function addMessage($type, array $arguments = [], $ignoreAJAX = false)
    {
        return $this->isConsentRevoked()
            ? false
            : parent::addMessage($type, $arguments, $ignoreAJAX);
    }
}
