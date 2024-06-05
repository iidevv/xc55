<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

use XC\MigrationWizard\Logic\Import\Processor\XCart\Configuration;

/**
 * Email settings
 */
class Email extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
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
            'smtp_auth_method' => [
                static::CONFIG_FIELD_NAME => 'use_smtp_auth',
            ],
            'use_smtp'         => [
                static::CONFIG_FIELD_NAME => 'use_smtp',
            ],
            'smtp_user'        => [
                static::CONFIG_FIELD_NAME => 'smtp_username',
            ],
            'smtp_server'      => [
                static::CONFIG_FIELD_NAME => 'smtp_server_url',
            ],
            'smtp_port'        => [
                static::CONFIG_FIELD_NAME => 'smtp_server_port',
            ],
            'smtp_protocol'    => [
                static::CONFIG_FIELD_NAME => 'smtp_security',
            ],
            'smtp_password'    => [
                static::CONFIG_FIELD_NAME => 'smtp_password',
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldUseSmtpAuthValue($value)
    {
        return Configuration::getConfigurationOptionValue('smtp_password') !== '' ? 'Y' : 'N';
    }

    public function normalizeFieldUseSmtpValue($value)
    {
        return $value === 'Y';
    }

    // }}} </editor-fold>
}
