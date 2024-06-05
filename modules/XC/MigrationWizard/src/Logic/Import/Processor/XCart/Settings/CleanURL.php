<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Settings;

/**
 * Clean URL settings
 */
class CleanURL extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return  [
            'clean_urls_enabled' =>  [
                static::CONFIG_FIELD_NAME => 'clean_url_flag',
            ]
        ];
    }

    // }}} </editor-fold>

    // {{{ Normalizators <editor-fold desc="Normalizators" defaultstate="collapsed">

    public function normalizeFieldCleanUrlFlagValue($value)
    {
        return $value === 'Y';
    }

    // }}} </editor-fold>
}
