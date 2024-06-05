<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * Company settings
 */
class Company extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'company_name'    => [
                static::CONFIG_FIELD_NAME => 'company_name',
            ],
            'company_website' => [
                static::CONFIG_FIELD_NAME => 'company_website',
            ],
            'start_year'      => [
                static::CONFIG_FIELD_NAME => 'start_year',
            ],
            'company_phone'   => [
                static::CONFIG_FIELD_NAME => 'company_phone',
            ],
            'company_fax'     => [
                static::CONFIG_FIELD_NAME => 'company_fax',
            ],

            'location_address' => [
                static::CONFIG_FIELD_NAME => 'location_address',
            ],
            'location_country' => [
                static::CONFIG_FIELD_NAME => 'location_country',
            ],
            'location_state'   => [
                static::CONFIG_FIELD_NAME => 'location_state',
            ],
            'location_city'    => [
                static::CONFIG_FIELD_NAME => 'location_city',
            ],
            'location_zipcode' => [
                static::CONFIG_FIELD_NAME => 'location_zipcode',
            ],

            'site_administrator' => [
                static::CONFIG_FIELD_NAME => 'site_administrator',
            ],
            'users_department'   => [
                static::CONFIG_FIELD_NAME => 'users_department',
            ],
            'orders_department'  => [
                static::CONFIG_FIELD_NAME => 'orders_department',
            ],
            'support_department' => [
                static::CONFIG_FIELD_NAME => 'support_department',
            ],

            'shipfrom_address_as_company_one' => [
                static::CONFIG_FIELD_NAME => 'origin_use_company',
            ],
            'shipfrom_address'                => [
                static::CONFIG_FIELD_NAME => 'origin_address',
            ],
            'shipfrom_country'                => [
                static::CONFIG_FIELD_NAME => 'origin_country',
            ],
            'shipfrom_state'                  => [
                static::CONFIG_FIELD_NAME => 'origin_state',
            ],
            'shipfrom_city'                   => [
                static::CONFIG_FIELD_NAME => 'origin_city',
            ],
            'shipfrom_zipcode'                => [
                static::CONFIG_FIELD_NAME => 'origin_zipcode',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeValueAsCountry($value)
    {
        \XLite\Core\Config::getInstance()->Company->location_country = $value;

        return $value;
    }

    public function normalizeValueAsState($value)
    {
        $country = \XLite\Core\Config::getInstance()->Company->location_country;

        $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode($country, $value);

        if ($state) {
            return $state->getStateId();
        }

        return \XLite\Core\Config::getInstance()->Company->location_state;
    }

    public function normalizeFieldLocationCountryValue($value)
    {
        return $this->normalizeValueAsCountry($value);
    }

    public function normalizeFieldLocationStateValue($value)
    {
        return $this->normalizeValueAsState($value);
    }

    public function normalizeFieldOriginCountryValue($value)
    {
        return $this->normalizeValueAsCountry($value);
    }

    public function normalizeFieldOriginStateValue($value)
    {
        return $this->normalizeValueAsState($value);
    }

    public function normalizeFieldOriginUseCompanyValue($value)
    {
        return $this->normalizeValueAsBoolean($value);
    }

    public function normalizeFieldSiteAdministratorValue($value)
    {
        return serialize([$value]);
    }

    public function normalizeFieldUsersDepartmentValue($value)
    {
        return serialize([$value]);
    }

    public function normalizeFieldOrdersDepartmentValue($value)
    {
        return serialize([$value]);
    }

    public function normalizeFieldSupportDepartmentValue($value)
    {
        return serialize([$value]);
    }

    // }}} </editor-fold>
}
