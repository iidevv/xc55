<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Model;

/**
 * PaypalAdvanced
 */
class PaypalAdaptive extends \CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = [
        'app_id' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Application ID',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'paypal_login' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'PayPal login (email)',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true
        ],
        'api_username' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API access username',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'api_password' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API access password',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'signature' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'API signature',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'feesPayer' => [
            self::SCHEMA_CLASS    => 'CDev\Paypal\View\FormField\Select\FeesPayer',
            self::SCHEMA_LABEL    => 'Fees payer',
            self::SCHEMA_HELP     => 'See more details here <a href="https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF">https://developer.paypal.com/docs/classic/adaptive-payments/integration-guide/APIntro/#id091QF0N0MPF</a>',
            self::SCHEMA_REQUIRED => true,
        ],
        'matchCriteria' => [
            self::SCHEMA_CLASS    => 'CDev\Paypal\View\FormField\Select\MatchCriteria',
            self::SCHEMA_LABEL    => 'Additional criteria to match for PayPal account verification',
            self::SCHEMA_HELP     => 'MATCH_CRITERIA_HELP',
            self::SCHEMA_REQUIRED => true,
        ],
    ];

    /**
     * Schema of the "Additional settings" section
     *
     * @var array
     */
    protected $schemaAdditional = [
        'mode' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Select\TestLiveMode',
            self::SCHEMA_LABEL    => 'Test/Live mode',
            self::SCHEMA_REQUIRED => false,
        ],
        'prefix' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Order id prefix',
            self::SCHEMA_HELP     => 'You can define an order id prefix, which would precede each order number in your shop, to make it unique',
            self::SCHEMA_REQUIRED => false,
        ],
    ];
}
