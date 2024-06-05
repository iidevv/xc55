<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use XLite\Model\Role\Permission;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params Handler params OPTIONAL
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['special_offers'])) {
            $this->relatedTargets['special_offers'] = [];
        }

        $this->relatedTargets['special_offers'][] = 'special_offer';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        $list['promotions'][static::ITEM_CHILDREN]['special_offers'] = [
            static::ITEM_TITLE      => static::t('Special offers'),
            static::ITEM_TARGET     => 'special_offers',
            static::ITEM_PERMISSION => Permission::ROOT_ACCESS,
            static::ITEM_WEIGHT     => 150,
        ];

        return $list;
    }
}
