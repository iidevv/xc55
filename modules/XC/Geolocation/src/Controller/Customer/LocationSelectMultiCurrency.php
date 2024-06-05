<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\MultiCurrency")
 */
class LocationSelectMultiCurrency extends \XC\Geolocation\Controller\Customer\LocationSelect
{
    /**
     * Set estimate destination
     *
     * @return void
     */
    protected function doActionChangeLocation()
    {
        $countryCode = \XLite\Core\Request::getInstance()->address_country;

        $country = (!$countryCode && !$this->hasField('country_code'))
            ? \XLite\Model\Address::getDefaultFieldValue('country')
            : $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($countryCode);

        $changeCountry = isset($country)
            && $country->getEnabled();

        if ($changeCountry) {
            $this->setHardRedirect(true);
            \XC\MultiCurrency\Core\MultiCurrency::getInstance()->setSelectedCountry($country);
        }

        parent::doActionChangeLocation();
    }
}
