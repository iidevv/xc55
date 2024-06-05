<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View\Button;

/**
 * Delete address button widget
 */
class AddressModify extends \XLite\View\Button\APopupButton
{
    /*
     * Address identificator parameter
     */
    public const PARAM_ADDRESS_TYPE = 'type';
    public const PARAM_WIDGET_TITLE = 'widgetTitle';

    /**
     * getJSFiles
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/XC/FastLaneCheckout/button/address_modify.js';

        return $list;
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
            self::PARAM_ADDRESS_TYPE => new \XLite\Model\WidgetParam\TypeString('Address type', null),
            self::PARAM_WIDGET_TITLE => new \XLite\Model\WidgetParam\TypeString('Widget title', static::t('Edit address')),
        ];
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return [
            'target'       => 'checkout',
            'type'         => $this->getParam(self::PARAM_ADDRESS_TYPE),
            'widget'       => 'XC\FastLaneCheckout\View\Blocks\PopupAddressForm',
            'widget_title' => $this->getParam(self::PARAM_WIDGET_TITLE)
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return parent::getClass() . ' address-modify';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'button/popup_link.twig';
    }

    /**
     * getDefaultStyle
     *
     * @return string
     */
    protected function getDefaultButtonClass()
    {
        return '';
    }

    /**
     * Define the button type (btn-warning and so on)
     *
     * @return string
     */
    protected function getDefaultButtonType()
    {
        return '';
    }
}
