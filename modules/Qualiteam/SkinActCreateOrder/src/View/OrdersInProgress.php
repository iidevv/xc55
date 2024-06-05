<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="admin.center", zone="admin")
 */
class OrdersInProgress extends \XLite\View\AView
{

    protected function getAttributes(): array
    {
        return [
            'data-widget' => 'Qualiteam\SkinActCreateOrder\View\OrdersInProgress'
        ];
    }

    public static function getAllowedTargets()
    {
        $result = [];
        $result[] = 'orders_in_progress';

        return $result;
    }


    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/OrdersInProgress/search.twig';
    }

    protected function isSearchVisible()
    {
        return true;
    }
}