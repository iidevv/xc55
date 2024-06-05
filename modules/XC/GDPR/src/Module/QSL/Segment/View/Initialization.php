<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace  XC\GDPR\Module\QSL\Segment\View;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * Initialization
 *
 * @Extender\Mixin
 * @Extender\Depend ("QSL\Segment")
 * @ListChild (list="head", zone="customer")
 */
class Initialization extends \QSL\Segment\View\Initialization
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
     * Get settings
     *
     * @return array
     */
    protected function getSettings()
    {
        $settings = parent::getSettings();

        if ($this->isConsentRevoked()) {
            $settings['allowed'] = [];
            $settings['messages'] = [];
        }

        return $settings;
    }
}
