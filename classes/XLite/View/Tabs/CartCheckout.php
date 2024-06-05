<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * Tabs related to localization
 *
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class CartCheckout extends \XLite\View\Tabs\ATabs
{
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'general_settings';
        $list[] = 'address_fields';
        $list[] = 'shipping_settings';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'general_settings' => [
                'weight'   => 100,
                'title'    => static::t('General'),
                'template' => 'settings/body.twig',
            ],
            'address_fields' => [
                'weight'   => 200,
                'title'    => static::t('Address fields'),
                'widget'   => '\XLite\View\ItemsList\Model\Address\Fields',
            ],
            'shipping_settings' => [
                'weight'   => 300,
                'title'    => static::t('Default customer address'),
                'template' => 'settings/body.twig',
            ],
        ];
    }
}
