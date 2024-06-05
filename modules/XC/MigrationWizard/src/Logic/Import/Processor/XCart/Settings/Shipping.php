<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * Shipping address settings
 */
class Shipping extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
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
            'default_country' => [
                static::CONFIG_FIELD_NAME => 'anonymous_country',
            ],
            'default_state'   => [
                static::CONFIG_FIELD_NAME => 'anonymous_state',
            ],
            'default_city'    => [
                static::CONFIG_FIELD_NAME => 'anonymous_city',
            ],
            'default_zipcode' => [
                static::CONFIG_FIELD_NAME => 'anonymous_zipcode',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldAnonymousCountryValue($value)
    {
        \XLite\Core\Config::getInstance()->Shipping->anonymous_state = $value;

        return $value;
    }

    public function normalizeFieldAnonymousStateValue($value)
    {
        $country = \XLite\Core\Config::getInstance()->Shipping->anonymous_country;

        $state = \XLite\Core\Database::getRepo('XLite\Model\State')->findOneByCountryAndCode($country, $value);

        if ($state) {
            return $state->getStateId();
        }

        return \XLite\Core\Config::getInstance()->Shipping->anonymous_state;
    }

    // }}} </editor-fold>
}
