<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\Model;

use XCart\Extender\Mapping\Extender;

/**
 * Settings
 * @Extender\Mixin
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        switch ($option->getName()) {
            case 'commit':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_HIDE => [
                        'record_transactions' => [false],
                    ],
                ];
                break;
        }

        return $cell;
    }

    /**
     * Populate model object properties by the passed data
     *
     * @param array $data Data to set
     *
     * @return void
     */
    protected function setModelProperties(array $data)
    {
        if (
            \XLite\Core\Request::getInstance()->page == 'Company'
            && isset($data['location_country'])
            && \XC\AvaTax\Core\TaxCore::getInstance()->isValid()
        ) {
            $address = [
                'location_address' => $data['location_address'],
                'location_city'    => $data['location_city'],
                'location_state'   => $data['location_state'],
                'location_country' => $data['location_country'],
                'location_zipcode' => $data['location_zipcode'],
            ];

            if (\XC\AvaTax\Core\TaxCore::getInstance()->isAllowedAddressVerification($address)) {
                [$address, $messages] = \XC\AvaTax\Core\TaxCore::getInstance()->validateAddress($address);

                if ($address) {
                    $data = array_merge($data, $address);
                }

                if ($messages) {
                    foreach ($messages as $message) {
                        $this->addErrorMessage($message['name'], $message['message'], $data);
                    }
                }
            }
        }

        parent::setModelProperties($data);
    }
}
