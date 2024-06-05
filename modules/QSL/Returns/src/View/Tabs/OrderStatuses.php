<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Tabs;

use XCart\Extender\Mapping\Extender;
use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 * @Extender\Depend ("!XC\CustomOrderStatuses")
 */
class OrderStatuses extends \XLite\View\Tabs\ATabs
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'return_reasons';
        $list[] = 'return_actions';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        return [
            'return_reasons'  => [
                'weight'     => 100,
                'title'      => static::t('Return reasons'),
                'widget'     => 'QSL\Returns\View\ItemsList\Model\ReturnReason',
            ],
            'return_actions' => [
                'weight'     => 200,
                'title'      => static::t('Return actions'),
                'widget'     => 'QSL\Returns\View\ItemsList\Model\ReturnAction',
            ],
        ];
    }
}
