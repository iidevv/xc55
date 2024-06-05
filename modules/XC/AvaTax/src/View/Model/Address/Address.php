<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\AvaTax\View\Model\Address;

use XCart\Extender\Mapping\Extender;

/**
 * Address
 * @Extender\Mixin
 */
class Address extends \XLite\View\Model\Address\Address
{
    /**
     * Check if field is valid and (if needed) set an error message
     *
     * @param array  $data    Current section data
     * @param string $section Current section name
     *
     * @return void
     */
    protected function validateFields(array $data, $section)
    {
        parent::validateFields($data, $section);

        if (
            !$this->errorMessages
            && \XC\AvaTax\Core\TaxCore::getInstance()->isAllowedAddressVerification($data[static::SECTION_PARAM_FIELDS])
        ) {
            [$address, $messages] = \XC\AvaTax\Core\TaxCore::getInstance()->validateAddress($data[static::SECTION_PARAM_FIELDS]);
            if ($messages) {
                foreach ($messages as $message) {
                    \XLite\Core\TopMessage::getInstance()->addError($message['message']);
                }
            } elseif ($address) {
                $mapData = [];
                foreach ($address as $field) {
                    $parts = explode('_', $field->getName(), 2);
                    $mapData[$parts[1]] = $field->getValue();
                }

                $this->prepareObjectForMapping()->map($mapData);
            }
        }
    }
}
