<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Geolocation\Model\Base;

use XCart\Extender\Mapping\Extender;
use XC\Geolocation\Logic;

/**
 * Abstract address model
 * @Extender\Mixin
 */
abstract class Address extends \XLite\Model\Base\Address
{
    /**
     * Return default field value
     *
     * @param string $fieldName Field name
     *
     * @return string
     */
    public static function getDefaultFieldPlainValue($fieldName)
    {
        if (!isset(static::$defaultFieldValuesCache[$fieldName])) {
            $location = static::shouldAccessLocation()
                ? Logic\Geolocation::getInstance()->getLocation(new Logic\GeoInput\IpAddress())
                : null;

            switch ($fieldName) {
                case 'country_code':
                    $fieldValue = $location['country'] ?? parent::getDefaultFieldPlainValue($fieldName);
                    break;

                case 'state_id':
                    $fixedName = 'state';
                    $stateCode = $location[$fixedName] ?? null;
                    $state = $stateCode ? \XLite\Core\Database::getRepo('XLite\Model\State')->findOneBy(['code' => $stateCode]) : null;
                    $fieldValue = $state ? $state->getStateId() : parent::getDefaultFieldPlainValue($fieldName);
                    break;

                case 'street':
                    $fieldValue = $location['address'] ?? parent::getDefaultFieldPlainValue($fieldName);
                    break;

                default:
                    $fieldValue = $location[$fieldName] ?? parent::getDefaultFieldPlainValue($fieldName);
                    break;
            }

            static::$defaultFieldValuesCache[$fieldName] = $fieldValue;
        }

        return static::$defaultFieldValuesCache[$fieldName];
    }

    /**
     * Returns true if geolocation should be accessed
     */
    public static function shouldAccessLocation()
    {
        return ! \XLite::getController() instanceof \XLite\Controller\Customer\ACheckoutReturn;
    }

    /**
     * Get address_id
     *
     * @return integer
     */
    public function getAddressId()
    {
        return $this->address_id;
    }

    /**
     * Set address_type
     *
     * @param string $addressType
     * @return Address
     */
    public function setAddressType($addressType)
    {
        $this->address_type = $addressType;
        return $this;
    }

    /**
     * Get address_type
     *
     * @return string
     */
    public function getAddressType()
    {
        return $this->address_type;
    }

    /**
     * Get country
     *
     * @return \XLite\Model\Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}
