<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to payment settings
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class PaymentSettings extends \XLite\View\Tabs\ATabs
{
    /**
     * Returns the list of targets where this widget is available
     *
     * @return string
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'payment_settings';
        $list[] = 'payment_appearance';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'payment_settings' => [
                'weight'   => 100,
                'title'    => static::t('Methods'),
                'template' => 'payment/configuration.twig',
            ],
            'payment_appearance' => [
                'weight'   => 200,
                'title'    => static::t('Names, descriptions & sorting'),
                'widget'    => '\XLite\View\ItemsList\Model\Payment\Methods',
            ],
        ];
    }
}
