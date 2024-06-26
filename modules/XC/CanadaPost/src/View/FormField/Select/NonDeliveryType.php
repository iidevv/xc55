<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CanadaPost\View\FormField\Select;

/**
 * Non-delivery handling selector
 */
class NonDeliveryType extends \XLite\View\FormField\Select\Regular
{
    /**
     * Widget param names
     */
    public const PARAM_IS_MANDATORY    = 'isMandatory';
    public const PARAM_ALLOWED_OPTIONS = 'allowedOptions';

    /**
     * Get default options for selector
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $options = \XC\CanadaPost\Model\Order\Parcel::getValidOptionsByClass(
            \XC\CanadaPost\Model\Order\Parcel::OPT_CLASS_NON_DELIVERY
        );

        $list = [];

        foreach ($options as $k => $v) {
            $list[$k] = $v[\XC\CanadaPost\Model\Order\Parcel::OPT_SCHEMA_TITLE];
        }

        return $list;
    }

    /**
     * Get options for selector
     *
     * @return array
     */
    protected function getOptions()
    {
        $list = parent::getOptions();

        foreach ($list as $k => $v) {
            if (!$this->isAllowedOption($k)) {
                // Remove not allowed option
                unset($list[$k]);
            }
        }

        if (!$this->isMandatory()) {
            // Add 'default' option if field is not mandatory
            $list = array_merge(
                ['' => static::t('Not specified')],
                $list
            );
        }

        return $list;
    }

    /**
     * Check - is option is allowed or not
     *
     * @param string $code Options code
     *
     * @return bool
     */
    protected function isAllowedOption($code)
    {
        $result = true;

        $allowedOptions = $this->getParam(static::PARAM_ALLOWED_OPTIONS);

        if (
            isset($allowedOptions)
            && !isset($allowedOptions[$code])
        ) {
            $result = false;
        }

        return $result;
    }

    /**
     * Check - is field is mandatory (one of the options should be selected)
     *
     * @return boolean
     */
    protected function isMandatory()
    {
        return $this->getParam(static::PARAM_IS_MANDATORY);
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_IS_MANDATORY    => new \XLite\Model\WidgetParam\TypeBool(
                'Is mandatory',
                false,
                false
            ),
            static::PARAM_ALLOWED_OPTIONS => new \XLite\Model\WidgetParam\TypeCollection(
                'Allowed options',
                null
            ),
        ];
    }
}
