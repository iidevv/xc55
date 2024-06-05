<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\Controller\Admin;

/**
 * FedEx module settings page controller
 */
class Fedex extends \XLite\Controller\Admin\ShippingSettings
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
            if ($option->getName() !== 'cod_type' || $this->isFedExCODPaymentEnabled()) {
                $list[] = $option;
            }

            if ($option->getName() === 'cacheOnDeliverySeparator') {
                $list[] = new \XLite\Model\Config([
                    'name'        => 'cod_status',
                    'type'        => 'XLite\View\FormField\Input\Checkbox\OnOff',
                    'value'       => $this->isFedExCODPaymentEnabled() ? true : false,
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
        return 'CDev\FedEx';
    }

    /**
     * Class name for the \XLite\View\Model\ form (optional)
     *
     * @return string|void
     */
    protected function getModelFormClass()
    {
        return 'CDev\FedEx\View\Model\Settings';
    }


    /**
     * Get shipping processor
     *
     * @return \XLite\Model\Shipping\Processor\AProcessor
     */
    protected function getProcessor()
    {
        return new \CDev\FedEx\Model\Shipping\Processor\FEDEX();
    }

    /**
     * Returns request data
     *
     * @return array
     */
    protected function getRequestData()
    {
        $list = parent::getRequestData();
        $list['dimensions'] = serialize($list['dimensions']);

        return $list;
    }

    /**
     * Check if 'Cash on delivery (FedEx)' payment method enabled
     * @todo: rename
     *
     * @return boolean
     */
    protected function isFedExCODPaymentEnabled()
    {
        return \CDev\FedEx\Model\Shipping\Processor\FEDEX::isCODPaymentEnabled();
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

        foreach (['srcAddress', 'dstAddress'] as $address) {
            if (isset($data[$address]['state'])) {
                if (
                    !empty($data[$address]['state'])
                    && !empty($data[$address]['country'])
                    && !in_array($data[$address]['country'], ['US', 'CA'])
                ) {
                    $data[$address]['state'] = '';
                }
            }
        }

        $data['packages'] = [$package];

        unset($data['weight'], $data['subtotal']);

        return $data;
    }
}
