<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Button;

use XCart\Extender\Mapping\Extender;

/**
 * Add address button widget
 * @Extender\Mixin
 */
abstract class AddAddress extends \XLite\View\Button\AddAddress
{
    /*
     * Profile identificator parameter
     */
    public const PARAM_ADDRESS_TYPE = 'atype';
    public const PARAM_SAVE_AND_APPLY = 'saveAndApply';

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_ADDRESS_TYPE => new \XLite\Model\WidgetParam\TypeString('Address type', null),
            self::PARAM_SAVE_AND_APPLY => new \XLite\Model\WidgetParam\TypeBool('Save and apply mode', false),
        ];
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        $params = [];

        if ($this->getParam(static::PARAM_SAVE_AND_APPLY)) {
            $params['requestedAction'] = 'save_and_apply';
            $params['atype'] = $this->getParam(self::PARAM_ADDRESS_TYPE);
        }

        return array_merge(
            parent::prepareURLParams(),
            $params
        );
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' add-address';
    }
}
