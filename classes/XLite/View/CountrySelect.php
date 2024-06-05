<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

// FIXME - to remove

/**
 * \XLite\View\CountrySelect
 */
class CountrySelect extends \XLite\View\FormField
{
    /**
     * Widget param names
     */

    public const PARAM_ALL        = 'all';
    public const PARAM_FIELD_NAME = 'field';
    public const PARAM_COUNTRY    = 'country';
    public const PARAM_FIELD_ID   = 'fieldId';
    public const PARAM_CLASS_NAME = 'className';
    public const PARAM_SELECT_ONE = 'selectOne';
    public const PARAM_ALLOW_LABEL_COUNTRY = 'allowLabelCountry';


    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'common/select_country.twig';
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ALL        => new \XLite\Model\WidgetParam\TypeBool('All', false),
            static::PARAM_FIELD_NAME => new \XLite\Model\WidgetParam\TypeString('Field name', ''),
            static::PARAM_FIELD_ID   => new \XLite\Model\WidgetParam\TypeString('Field ID', ''),
            static::PARAM_CLASS_NAME => new \XLite\Model\WidgetParam\TypeString('Class name', ''),
            static::PARAM_COUNTRY    => new \XLite\Model\WidgetParam\TypeString('Value', ''),
            static::PARAM_SELECT_ONE => new \XLite\Model\WidgetParam\TypeBool('Select one value', false),
            static::PARAM_ALLOW_LABEL_COUNTRY => new \XLite\Model\WidgetParam\TypeBool('Allow label-based country selector', false),
        ];
    }

    /**
     * Check - display enabled only countries or not
     *
     * @return boolean
     */
    protected function isEnabledOnly()
    {
        return !$this->getParam(static::PARAM_ALL);
    }

    /**
     * Get selected value
     *
     * @return string
     */
    protected function getSelectedValue()
    {
        return $this->getParam(static::PARAM_COUNTRY);
    }

    /**
     * Check - if country code is selected option in "SELECT" tag.
     *
     * @param string $countryCode Code of country to check.
     *
     * @return boolean
     */
    protected function isSelectedCountry($countryCode)
    {
        $country = $this->getParam(static::PARAM_COUNTRY);

        if ($country == '') {
            $countryObj = \XLite\Model\Address::getDefaultFieldValue('country');
            $country = $countryObj
                ? $countryObj->getCode()
                : '';
        }

        return $country === $countryCode;
    }

    /**
     * Return countries list
     *
     * @return array
     */
    protected function getCountries()
    {
        return $this->isEnabledOnly()
            ? \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllEnabled()
            : \XLite\Core\Database::getRepo('XLite\Model\Country')->findAllCountries();
    }

    /**
     * Check - country selector is label-based
     *
     * @return boolean
     */
    protected function isLabelBasedSelector()
    {
        return $this->getParam(static::PARAM_ALLOW_LABEL_COUNTRY)
            && count($this->getCountries()) == 1;
    }

    /**
     * Get one country
     *
     * @return \XLite\Model\Country
     */
    protected function getOneCountry()
    {
        $list = $this->getCountries();

        return reset($list);
    }

    /**
     * Return if the select one value is available
     *
     * @return boolean
     */
    protected function hasSelectOne()
    {
        return $this->getParam(static::PARAM_SELECT_ONE);
    }
}
