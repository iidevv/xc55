<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View\Menu\Admin;

use XCart\Extender\Mapping\Extender as Extender;

/**
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    public function __construct(array $params = [])
    {
        $this->relatedTargets['pro_membership'] = [];

        parent::__construct($params);
    }

    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['pro_membership'] = [
            static::ITEM_TITLE    => static::t('SkinActProMembership pro membership menu'),
            static::ITEM_ICON_SVG => 'images/left_menu/promotions.svg',
            static::ITEM_WEIGHT   => $items['catalog'][static::ITEM_WEIGHT] + 50,
            static::ITEM_CHILDREN => [],
        ];

        return $items;
    }
}