<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FedEx\View\Model;

/**
 * FedEx configuration form model
 */
class Settings extends \XLite\View\Model\AShippingSettings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/CDev/FedEx/settings.css';

        return $list;
    }

    /**
     * Detect form field class by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return string
     */
    protected function detectFormFieldClassByOption(\XLite\Model\Config $option)
    {
        return $option->getName() === 'dimensions'
            ? 'XLite\View\FormField\Input\Text\Dimensions'
            : parent:: detectFormFieldClassByOption($option);
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
            case 'cod_status':
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_DISABLED] = true;
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_ON_LABEL] = static::t('paymentStatus.Active');
                $cell[\XLite\View\FormField\Input\Checkbox\OnOff::PARAM_OFF_LABEL] = static::t('paymentStatus.Inactive');
                $cell[static::SCHEMA_COMMENT] = static::t(
                    'fedex.CODStatusOptionComment',
                    [
                        'URL' => $this->buildURL('payment_settings')
                    ]
                );
                break;

            case 'fxsp_hub_id':
            case 'fxsp_indicia':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'fxsp' => [true],
                    ],
                ];
                break;

            case 'dg_accessibility':
                $cell[static::SCHEMA_DEPENDENCY] = [
                    static::DEPENDENCY_SHOW => [
                        'fxsp' => [false],
                    ],
                ];
                break;
        }

        return $cell;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $value = parent::getModelObjectValue($name);
        if ($name === 'dimensions') {
            $value = unserialize($value);
        }

        return $value;
    }

    /**
     * Get editable options
     *
     * @return array
     */
    protected function getEditableOptions()
    {
        $list = parent::getEditableOptions();
        foreach ($list as $k => $option) {
            if ($option->getName() === 'cod_status') {
                unset($list[$k]);
            }
        }

        return $list;
    }
}
