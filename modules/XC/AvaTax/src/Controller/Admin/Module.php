<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Module settings
 * @Extender\Mixin
 */
abstract class Module extends \XLite\Controller\Admin\Module
{
    /**
     * Preprocessor for no-action run
     *
     * @return void
     */
    protected function doNoAction()
    {
        parent::doNoAction();

        if (
            $this->getModule() === 'XC-AvaTax'
            && \XLite\Core\Config::getInstance()->XC->AvaTax->account_number
            && \XLite\Core\Config::getInstance()->XC->AvaTax->license_key
        ) {
            // Check connection
            $messages = [];
            if (!\XC\AvaTax\Core\TaxCore::getInstance()->testConnection($messages)) {
                \XLite\Core\TopMessage::addError('Connection to AvaTax server failed');
                foreach ($messages as $message) {
                    \XLite\Core\TopMessage::addError($message);
                }
            } else {
                // Check address
                $company = \XLite\Core\Config::getInstance()->Company;
                $address = [
                    'location_address' => $company->location_address,
                    'location_city'    => $company->location_city,
                    'location_state'   => $company->location_state,
                    'location_country' => $company->location_country,
                    'location_zipcode' => $company->location_zipcode,
                ];
                [$address, $messages] = \XC\AvaTax\Core\TaxCore::getInstance()->validateAddress($address);
                if ($messages) {
                    \XLite\Core\TopMessage::addError(
                        'Invalid company address. Please follow this link and correct the address.',
                        [
                            'url' => \XLite\Core\COnverter::buildURL('settings', null, ['page' => 'Company']),
                        ]
                    );
                }
            }
        }
    }
}
