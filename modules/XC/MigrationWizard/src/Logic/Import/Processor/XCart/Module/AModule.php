<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Abstract module
 */
abstract class AModule extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Settings\ASettings
{
    // {{{ Constants <editor-fold desc="Constants" defaultstate="collapsed">

    public const MODULE_FIELD_NAME          = parent::CONFIG_FIELD_NAME;
    public const MODULE_FIELD_IS_REQUIRED   = parent::CONFIG_FIELD_IS_REQUIRED;
    public const MODULE_FIELD_IS_VIRTUAL    = parent::CONFIG_FIELD_IS_VIRTUAL;
    public const MODULE_FIELD_INITIALIZATOR = parent::CONFIG_FIELD_INITIALIZATOR;
    public const MODULE_FIELD_NORMALIZATOR  = parent::CONFIG_FIELD_NORMALIZATOR;

    // }}} </editor-fold>

    // {{{ Properties <editor-fold desc="Properties" defaultstate="collapsed">

    /**
     * Setting creation rule
     *
     * @var string
     */
    protected $createRule = self::CONFIG_SETTING_CAN_CREATE;

    // }}} </editor-fold>

    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return __CLASS__;
    }

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineRequiredModules()
    {
        return [static::defineModuleName()];
    }

    /**
     * Define category name
     *
     * @return string
     */
    public static function defineCategoryName()
    {
        return static::defineModuleName();
    }

    // }}} </editor-fold>
}
