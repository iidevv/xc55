<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\View\Alert;

/**
 * Shipping settings warning
 */
class ShippingSettingsWarning extends \XLite\View\Alert\Warning
{
    protected function isVisible()
    {
        return \Includes\Utils\Module\Manager::getRegistry()->isModuleEnabled('XC', 'Geolocation');
    }

    protected function getAlertContent()
    {
        return static::t(
            'Your store uses the addon Geolocation',
            ['geoip-settings-link' => \Includes\Utils\Module\Manager::getRegistry()->getModuleSettingsUrl('XC', 'Geolocation')]
        );
    }
}
