<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Model;

use Doctrine\ORM\Mapping as ORM;
use XC\Geolocation\Logic;
use XCart\Extender\Mapping\Extender;

/**
 * @ORM\HasLifecycleCallbacks
 * @Extender\Mixin
 */
abstract class Address extends \XLite\Model\Address
{
    /**
     * Get default value for the field
     *
     * @param string $fieldName Field service name
     *
     * @return mixed
     */
    public static function getDefaultFieldValue($fieldName)
    {
        $result = null;
        $location = static::shouldAccessLocation()
            ? Logic\Geolocation::getInstance()->getLocation(new Logic\GeoInput\IpAddress())
            : null;

        if ($location) {
            $fieldValue = $location[$fieldName] ?? null;

            switch ($fieldName) {
                case 'country':
                    if ($fieldValue) {
                        $result = \XLite\Core\Database::getRepo('XLite\Model\Country')->findOneByCode($fieldValue);
                        $result = $result ?: null;
                    }
                    break;

                case 'state':
                    if ($fieldValue) {
                        $result = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneBy(['code' => $fieldValue]);
                        $result = $result ?: null;
                    }
                    if (!$result || (!$fieldValue && isset($location['country']))) {
                        return null;
                    }
                    break;

                case 'custom_state':
                case 'zipcode':
                case 'city':
                    $result = $fieldValue ?: '';
                    break;

                default:
            }
        }

        return $result ?: parent::getDefaultFieldValue($fieldName);
    }

    /**
     * Returns true if geolocation should be accessed
     */
    public static function shouldAccessLocation()
    {
        return !(\XLite::getController() instanceof \XLite\Controller\Customer\ACheckoutReturn);
    }
}
