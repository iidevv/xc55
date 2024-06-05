<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input;

use XLite\Model\Address;

/**
 * Radio
 */
class SearchByAddressRadio extends \XLite\View\FormField\Input\Checkbox
{
    public const PARAM_TYPE_ADDRESS = 'address';

    /**
     * Return field type
     *
     * @return string
     */
    public function getFieldType()
    {
        return self::FIELD_TYPE_RADIO;
    }

    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'search_by_address_radio.twig';
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return [
            'both'            => static::t('Both'),
            Address::SHIPPING => static::t('Shipping'),
            Address::BILLING  => static::t('Billing'),
        ];
    }

    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/search_by_address_radio.less';

        return $list;
    }

    /**
     * @return string
     */
    public function getWrapperClass()
    {
        return parent::getWrapperClass() . ' search-by-address-radio radio';
    }
}
