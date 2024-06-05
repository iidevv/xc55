<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Returns\View\Tabs;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class OrdersList extends \XLite\View\Tabs\OrdersList
{
    /**
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'order_returns';

        return $list;
    }

    /**
     * @return array
     */
    protected function defineTabs()
    {
        $list = parent::defineTabs();

        $list['order_returns'] = [
            'weight' => 300,
            'title'  => static::t('Returns'),
            'widget' => 'QSL\Returns\View\Page\Admin\OrderReturns'
        ];

        return $list;
    }
}
