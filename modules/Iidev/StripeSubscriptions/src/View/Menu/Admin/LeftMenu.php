<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Iidev\StripeSubscriptions\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * Left menu widget
 *
 * @Extender\Mixin
 */
class LeftMenu extends \XLite\View\Menu\Admin\LeftMenu
{
    /**
     * Returns the list of related targets
     *
     * @param string $target Target name
     *
     * @return array
     */
    public function getRelatedTargets($target)
    {
        $targets = parent::getRelatedTargets($target);

        if ('profile_list' == $target) {
            $targets[] = 'migration_stats';
        }

        return $targets;
    }

    /**
     * Define items
     *
     * @return array
     */
    protected function defineItems()
    {
        $list = parent::defineItems();

        if (isset($list['sales'])) {
            $list['sales'][static::ITEM_CHILDREN]['migration_stats'] = [
                static::ITEM_TITLE  => static::t('Migration stats'),
                static::ITEM_TARGET => 'migration_stats',
                static::ITEM_WEIGHT => 2000,
            ];
        }

        return $list;
    }
}
