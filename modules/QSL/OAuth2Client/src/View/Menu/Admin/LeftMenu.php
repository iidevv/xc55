<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\OAuth2Client\View\Menu\Admin;

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

        if (isset($list['store_setup'])) {
            $list['store_setup'][static::ITEM_CHILDREN]['oauth2_client_providers'] = [
                static::ITEM_TITLE  => static::t('OAuth 2 providers'),
                static::ITEM_TARGET => 'oauth2_client_providers',
                static::ITEM_WEIGHT => 10000,
            ];
        }

        return $list;
    }
}
