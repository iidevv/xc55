<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin", weight="100")
 */
class OrdersStats extends \XLite\View\AView
{
    /**
     * Return list of allowed targets
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list   = parent::getAllowedTargets();
        $list[] = 'orders_stats';

        return $list;
    }

    /**
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'orders_stats.twig';
    }
}
