<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;
use QSL\Banner\Controller\Admin\BannersList;

/**
 * @Extender\Mixin
 */
abstract class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        if (!isset($this->relatedTargets['banners_list'])) {
            $this->relatedTargets['banners_list'] = [];
        }

        $this->relatedTargets['banners_list'][] = 'banner_edit';

        parent::__construct();
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $items = parent::defineItems();

        $items['store_design'][self::ITEM_CHILDREN]['banners_list'] = [
            self::ITEM_TITLE      => static::t('Banners'),
            self::ITEM_TARGET     => 'banners_list',
            self::ITEM_PERMISSION => BannersList::PERMISSION_BANNERS,
            self::ITEM_WEIGHT     => 175,
        ];

        return $items;
    }
}
