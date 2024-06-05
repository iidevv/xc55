<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

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
        if (!isset($this->relatedTargets['catalog'])) {
            $this->relatedTargets['catalog'] = [];
        }

        $this->relatedTargets['catalog'][] = 'product_feeds';

        parent::__construct($params);
    }

    /**
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['catalog'])) {
            $list['catalog'][static::ITEM_CHILDREN]['product_feeds'] = [
                static::ITEM_TITLE  => static::t('Feeds'),
                static::ITEM_TARGET => 'product_feeds',
                static::ITEM_WEIGHT => 1000,
            ];
        }

        return $list;
    }
}
