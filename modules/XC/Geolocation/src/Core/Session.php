<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Core;

use XCart\Extender\Mapping\Extender;
use Includes\Utils\ArrayManager;

/**
 * Current session
 * @Extender\Mixin
 */
class Session extends \XLite\Core\Session
{
    /**
     * Define current language
     *
     * @return string Language code
     */
    protected function defineCurrentLanguage()
    {
        $languages = \XLite\Core\Database::getRepo('XLite\Model\Language')->findActiveLanguages();
        if (!\XLite::isAdminZone() && !empty($languages)) {
            $data = \XC\Geolocation\Logic\Geolocation::getInstance()->getLocation(new \XC\Geolocation\Logic\GeoInput\IpAddress());

            if (isset($data['country'])) {
                $country = \XLite\Core\Database::getRepo('XLite\Model\Country')->find($data['country']);

                if (
                    $country &&
                    $country->getLanguage()
                ) {
                    $result = ArrayManager::searchInObjectsArray(
                        $languages,
                        'getCode',
                        $country->getLanguage()->getCode(),
                        true
                    );
                }
            }
        }

        return isset($result)
            ? $result->getCode()
            : parent::defineCurrentLanguage();
    }
}
