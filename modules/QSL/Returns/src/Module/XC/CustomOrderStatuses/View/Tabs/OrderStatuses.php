<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\Module\XC\CustomOrderStatuses\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\CustomOrderStatuses")
 */
class OrderStatuses extends \XC\CustomOrderStatuses\View\Tabs\OrderStatuses
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
        $list = parent::defineTabs();

        $list['return_reasons'] = [
            'weight'     => 400,
            'title'      => static::t('Return reasons'),
            'widget'     => 'QSL\Returns\View\ItemsList\Model\ReturnReason',
        ];

        $list['return_actions'] = [
            'weight'     => 500,
            'title'      => static::t('Return actions'),
            'widget'     => 'QSL\Returns\View\ItemsList\Model\ReturnAction',
        ];

        return $list;
    }
}
