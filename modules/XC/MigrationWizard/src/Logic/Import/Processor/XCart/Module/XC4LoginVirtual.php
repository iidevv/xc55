<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Module;

/**
 * Enable XC4Login Module And Fill The Blowfish Key
 *
 * @author Ildar Amankulov <aim@x-cart.com>
 */
class XC4LoginVirtual extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Module\AModule
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define required modules list
     *
     * @return array
     */
    public static function defineModuleName()
    {
        return 'XC\XC4Login';
    }


    /**
     * Define config options list
     *
     * @return array
     */
    public static function defineConfigset()
    {
        return [
            'XC_blowfish_key'     => [
                static::MODULE_FIELD_NAME       => 'blowfish_key',
                static::MODULE_FIELD_IS_VIRTUAL => true,
            ],
        ];
    }

    // }}} </editor-fold>

    // {{{ Initializers <editor-fold desc="Initializers" defaultstate="collapsed">

    public static function initFieldBlowfishKeyValue()
    {
        $connectStep = static::getStepConnect();

        if ($connectStep && $secretKey = $connectStep->getSecret()) {
            return $secretKey;
        } else {
            return '';
        }
    }

    // }}} </editor-fold>
}
