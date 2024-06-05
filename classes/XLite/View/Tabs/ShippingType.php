<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;
use XLite\View\Button\Shipping\AddMethod;

/**
 * Tabs related to shipping settings
 *
 * @ListChild (list="add_shipping", zone="admin", weight="10")
 */
class ShippingType extends \XLite\View\Tabs\AJsTabs
{
    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'carrier_accounts'   => [
                'weight'   => 100,
                'title'    => 'Carrier Accounts',
                'template' => 'shipping/add_method/parts/carrier_accounts_list.twig',
            ],
            'shipping_solutions' => [
                'weight'   => 200,
                'title'    => 'Shipping Solutions',
                'template' => 'shipping/add_method/parts/shipping_solutions_list.twig',
            ],
            'offline'            => [
                'weight'   => 300,
                'title'    => 'Flat Shipping Rates',
                'template' => 'shipping/add_method/parts/offline_list.twig',
                'selected' => $this->getShippingType() === 'offline',
            ],
        ];
    }

    /**
     * Offline methods help template
     *
     * @return string
     */
    protected function getOfflineMethodsHelpTemplate()
    {
        return 'shipping/add_method/parts/offline_methods_help.twig';
    }

    /**
     * Offline methods JS template
     *
     * @return string
     */
    protected function getOfflineMethodsJSTemplate()
    {
        return 'shipping/add_method/parts/offline_list_script.twig';
    }

    /**
     * Carrier accounts help template
     *
     * @return string
     */
    protected function getCarrierAccountsHelpTemplate()
    {
        return 'shipping/add_method/parts/carrier_accounts_help.twig';
    }

    /**
     * Shipping solutions help template
     *
     * @return string
     */
    protected function getShippingSolutionsHelpTemplate()
    {
        return 'shipping/add_method/parts/shipping_solutions_help.twig';
    }

    /**
     * Return payment methods type which is provided to the widget
     *
     * @return string
     */
    protected function getShippingType()
    {
        return \XLite\Core\Request::getInstance()->{AddMethod::PARAM_SHIPPING_METHOD_TYPE};
    }

    /**
     * @return string
     */
    protected function getCarrierAccountsLink()
    {
        return \XLite::getInstance()->getAppStoreUrl() . 'addons/carrier-accounts/';
    }

    /**
     * @return string
     */
    protected function getShippingSolutionsLink()
    {
        return \XLite::getInstance()->getAppStoreUrl() . 'addons/shipping-solutions/';
    }
}
