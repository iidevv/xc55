<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class Shipping extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'shipping_methods',
                'shipping_sorting',
                'shipping_address',
            ]
        );
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible()
            && empty(\XLite\Core\Request::getInstance()->processor);
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'shipping_methods' => [
                'weight' => 100,
                'title'  => static::t('Methods'),
                'widget' => 'XLite\View\Shipping\ShippingMethods',
            ],
            'shipping_sorting' => [
                'weight' => 200,
                'title'  => static::t('Sorting'),
                'widget' => 'XLite\View\ItemsList\Model\Shipping\MethodsSorting',
            ],
            'shipping_address' => [
                'weight' => 300,
                'title'  => static::t('Origin address'),
                'widget' => 'XLite\View\Shipping\ShippingAddress',
            ],
        ];
    }
}
