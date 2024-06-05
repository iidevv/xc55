<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\View\Tabs;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */

class AbandonedCart extends \XLite\View\Tabs\ATabs
{
    /**
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'abandoned_carts';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'abandoned_carts' => [
                'weight' => 200,
                'title'  => static::t('Abandoned carts'),
                'widget' => 'QSL\AbandonedCartReminder\View\ItemsList\Model\AbandonedCart',
            ],
        ];
    }
}
