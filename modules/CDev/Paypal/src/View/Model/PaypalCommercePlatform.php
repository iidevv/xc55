<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Paypal\View\Model;

use CDev\Paypal\Core\PaypalCommercePlatformAPI;
use CDev\Paypal\View\FormField\Select\DisabledFundingMethods;

class PaypalCommercePlatform extends \CDev\Paypal\View\Model\ASettings
{
    /**
     * Schema of the "Your account settings" section
     *
     * @var array
     */
    protected $schemaAccount = [
        'merchant_id'   => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Merchant Id',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'client_id'     => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Client Id',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
        'client_secret' => [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Text',
            self::SCHEMA_LABEL    => 'Client Secret',
            self::SCHEMA_HELP     => '',
            self::SCHEMA_REQUIRED => true,
        ],
    ];

    /**
     * Save current form reference and initialize the cache
     *
     * @param array $params   Widget params OPTIONAL
     * @param array $sections Sections list OPTIONAL
     */
    public function __construct(array $params = [], array $sections = [])
    {
        parent::__construct($params, $sections);

        unset($this->schemaAdditional['prefix']);

        $this->schemaAdditional['disabledFundingMethods'] = [
            self::SCHEMA_CLASS    => DisabledFundingMethods::class,
            self::SCHEMA_LABEL    => 'Disable funding methods (checkout page)',
            self::SCHEMA_HELP     => 'When multiple funding sources are available to the buyer, PayPal automatically determines which additional buttons are appropriate to display. However, you can choose to opt-in or out-of displaying specific funding sources.',
            self::SCHEMA_REQUIRED => false,
        ];

        $this->schemaAdditional['3d_secure'] = [
            self::SCHEMA_CLASS    => 'XLite\View\FormField\Input\Checkbox\OnOff',
            self::SCHEMA_LABEL    => '3D Secure',
            self::SCHEMA_REQUIRED => false,
        ];
    }

    /**
     * @return string
     */
    protected function getFormClass()
    {
        return 'CDev\Paypal\View\Form\PaypalCommercePlatformSettings';
    }

    /**
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        $method = $this->getModelObject();

        if (isset($data['mode']) && $data['mode'] !== $method->getSetting('mode')) {
            PaypalCommercePlatformAPI::dropPayPalTokenCash();

            $data['merchant_id']   = '';
            $data['client_id']     = '';
            $data['client_secret'] = '';
        }

        if (!isset($data['disabledFundingMethods'])) {
            $data['disabledFundingMethods'] = null;
        }

        parent::setModelProperties($data);
    }
}
