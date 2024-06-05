<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GDPR\View;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;
use XC\ThemeTweaker\Core\ThemeTweaker;

/**
 * CookieConsent
 *
 * @Extender\Mixin
 * @Extender\Rely("XC\ThemeTweaker")
 */
class ThemeTweakerCookieConsent extends \XC\GDPR\View\CookieConsent
{
    /**
     * @return bool
     */
    protected function isVisible()
    {
        return parent::isVisible() || (ThemeTweaker::getInstance()->isInLabelsMode() && !Request::getInstance()->isAJAX());
    }
}
