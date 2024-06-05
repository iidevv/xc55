<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\View\Checkout;

use XCart\Extender\Mapping\Extender as Extender;
use XLite\Core\Cache\ExecuteCachedTrait;
use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Core\Session;
use XLite\Model\State;

/**
 * @Extender\Mixin
 */
abstract class AAddressBlock extends \XLite\View\Checkout\AAddressBlock
{
    use ExecuteCachedTrait;

    /**
     * Get field value
     *
     * @param string  $fieldName    Field name
     * @param boolean $processValue Process value flag OPTIONAL
     *
     * @return string
     */
    public function getFieldValue($fieldName, $processValue = false)
    {
        if (isset(Request::getInstance()->methodId)
            && Session::getInstance()->klarna_profile
        ) {
            if (in_array($fieldName, $this->getReplacedFields(), true)) {
                return $this->{'get' . \Includes\Utils\Converter::convertToUpperCamelCase($fieldName) . 'Value'}();
            }
        }

        return parent::getFieldValue($fieldName, $processValue);
    }

    /**
     * @return string[]
     */
    protected function getReplacedFields(): array
    {
        return [
            'firstname',
            'lastname',
            'email',
            'phone',
            'city',
            'street',
            'zipcode',
            'country_code',
            'state_id',
        ];
    }

    /**
     * @return string
     */
    protected function getFirstnameValue(): string
    {
        return Session::getInstance()->klarna_profile['first_name'];
    }

    /**
     * @return string
     */
    protected function getLastnameValue(): string
    {
        return Session::getInstance()->klarna_profile['last_name'];
    }

    /**
     * @return string
     */
    protected function getEmailValue(): string
    {
        return Session::getInstance()->klarna_profile['email'];
    }

    /**
     * @return string
     */
    protected function getPhoneValue(): string
    {
        return Session::getInstance()->klarna_profile['phone'];
    }

    /**
     * @return string
     */
    protected function getCityValue(): string
    {
        return Session::getInstance()->klarna_profile['address']['city'];
    }

    /**
     * @return string
     */
    protected function getStreetValue(): string
    {
        return Session::getInstance()->klarna_profile['address']['street_address'];
    }

    /**
     * @return string
     */
    protected function getZipcodeValue(): string
    {
        return Session::getInstance()->klarna_profile['address']['postal_code'];
    }

    /**
     * @return string
     */
    protected function getCountrycodeValue(): string
    {
        return Session::getInstance()->klarna_profile['address']['country'];
    }

    /**
     * @return string
     */
    protected function getStateIdValue(): string
    {
        $region = Session::getInstance()->klarna_profile['address']['region'];

        $dbRegion = $this->executeCachedRuntime(function() use ($region) {
            return Database::getRepo(State::class)
                ->findOneBy(['code' => $region]);
        }, [
            __CLASS__,
            __METHOD__,
            $region
        ]);

        return $dbRegion->getStateId();
    }
}