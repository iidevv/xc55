<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\UPS\Controller\Admin;

/**
 * UPS shipping module settings controller
 */
class Ups extends \XLite\Controller\Admin\ShippingSettings
{
    /**
     * Returns shipping options
     *
     * @return array
     */
    public function getOptions()
    {
        $list = [];
        foreach (parent::getOptions() as $option) {
            $list[] = $option;

            if ($option->getName() === 'cacheOnDeliverySeparator') {
                $list[] = new \XLite\Model\Config([
                    'name'        => 'cod_status',
                    'type'        => 'XLite\View\FormField\Input\Checkbox\OnOff',
                    'value'       => $this->isUPSCODPaymentEnabled() ? true : false,
                    'orderby'     => $option->getOrderby() + 1,
                    'option_name' => static::t('"Cash on delivery" status'),
                ]);
            }
        }

        return $list;
    }

    /**
     * getOptionsCategory
     *
     * @return string
     */
    protected function getOptionsCategory()
    {
        return 'XC\UPS';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|null
     */
    protected function getModelFormClass()
    {
        return 'XC\UPS\View\Model\Settings';
    }

    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \XC\UPS\Model\Shipping\Processor\UPS();
    }

    /**
     * Check if 'Cash on delivery (UPS)' payment method enabled
     *
     * @return boolean
     */
    public function isUPSCODPaymentEnabled()
    {
        return \XC\UPS\Model\Shipping\Processor\UPS::isCODPaymentEnabled();
    }

    /**
     * Get input data to calculate test rates
     *
     * @param array $schema  Input data schema
     * @param array &$errors Array of fields which are not set
     *
     * @return array
     */
    protected function getTestRatesData(array $schema, &$errors)
    {
        $data = parent::getTestRatesData($schema, $errors);

        $package = [
            'weight'   => $data['weight'],
            'subtotal' => $data['subtotal'],
        ];

        $data['packages'] = [];
        $data['packages'][] = $package;

        unset($data['weight'], $data['subtotal']);

        return $data;
    }
}
