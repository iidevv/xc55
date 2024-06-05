<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Shipping;

/**
 * ShippingAddress page view
 */
class ShippingAddress extends \XLite\View\Model\Settings
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'shipping_address';

        return $result;
    }

    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'shipping/edit_ship_from_address/controller.js';

        return $list;
    }

    /**
     * Get array of country/states selector fields which should be synchronized
     *
     * @return array
     */
    protected function getCountryStateSelectorFields()
    {
        return [
            'origin_country' => [
                'origin_state',
                'origin_custom_state',
            ],
        ];
    }

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
            case 'origin_address':
            case 'origin_country':
            case 'origin_state':
            case 'origin_custom_state':
            case 'origin_city':
            case 'origin_zipcode':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'origin_use_company' => [false],
                    ],
                ];
                break;
            case 'location_address':
            case 'location_country':
            case 'location_state':
            case 'location_custom_state':
            case 'location_city':
            case 'location_zipcode':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'origin_use_company' => [true],
                    ],
                ];
                break;
        }

        return $cell;
    }

    /**
     * @param array $data Array of field data
     *
     * @return array
     */
    protected function prepareFieldParamsLocationCountry($data)
    {
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_SELECTOR_ID] = 'location-state';
        $data[\XLite\View\FormField\Select\Country::PARAM_STATE_INPUT_ID]    = 'location-custom-state';

        return $data;
    }
}
