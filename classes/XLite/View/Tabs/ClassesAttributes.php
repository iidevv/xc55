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
class ClassesAttributes extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        return array_merge(
            parent::getAllowedTargets(),
            [
                'global_attributes',
                'product_classes'
            ]
        );
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'global_attributes' => [
                'weight' => 100,
                'title'  => static::t('All attributes'),
                'widget' => 'XLite\View\GlobalAttributes',
            ],
            'product_classes' => [
                'weight' => 200,
                'title'  => static::t('Product classes'),
                'widget' => 'XLite\View\ItemsList\Model\ProductClass',
            ],
        ];
    }
}
