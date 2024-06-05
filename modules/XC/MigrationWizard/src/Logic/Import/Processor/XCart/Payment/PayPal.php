<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Logic\Import\Processor\XCart\Payment;

/**
 * PayPal payment
 */
class PayPal extends \XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\APayment
{
    // {{{ Data definers <editor-fold desc="Data definers" defaultstate="collapsed">

    /**
     * Define sub processors
     *
     * @return array
     */
    public static function defineSubProcessors()
    {
        return [
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal\ExpressCheckout',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal\PayflowLink',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal\PayflowTransparentRedirect',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal\PaypalAdvanced',
            'XC\MigrationWizard\Logic\Import\Processor\XCart\Payment\PayPal\PaypalWPS',
        ];
    }

    /**
     * Define module name
     *
     * @return string
     */
    public static function defineModuleName()
    {
        return 'CDev\Paypal';
    }

    // }}} </editor-fold>
}
