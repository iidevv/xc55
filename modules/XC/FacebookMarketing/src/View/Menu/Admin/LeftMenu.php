<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FacebookMarketing\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['marketing'][static::ITEM_CHILDREN]['facebook_marketing'] = [
            static::ITEM_TITLE  => static::t('Facebook Ads & Instagram Ads'),
            static::ITEM_TARGET => 'facebook_marketing',
            static::ITEM_WEIGHT => 200,
        ];

        return $list;
    }
}
