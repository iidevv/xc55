<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class Groups extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'root_categories',
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'root_categories'         => [
                'weight'     => 100,
                'title'      => static::t('Categories'),
                'widget'    => 'XLite\View\ItemsList\Model\Category',
            ],
        ];
    }
}
