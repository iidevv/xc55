<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    public function __construct(array $params = [])
    {
        parent::__construct($params);

        $this->addRelatedTarget('gift_tier', 'free_gifts');
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        if (isset($items['catalog'][static::ITEM_CHILDREN])) {
            $items['catalog'][static::ITEM_CHILDREN]['free_gifts'] = [
                static::ITEM_TITLE  => static::t('SkinActFreeGifts Free Gifts'),
                static::ITEM_TARGET => 'free_gifts',
                static::ITEM_WEIGHT => 250,
            ];
        }

        return $items;
    }
}
