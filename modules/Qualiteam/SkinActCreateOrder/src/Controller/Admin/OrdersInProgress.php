<?php

// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\Controller\Admin;



class OrdersInProgress extends \XLite\Controller\Admin\OrderList
{
    public function getTitle()
    {
        return static::t('SkinActCreateOrder Orders in progress');
    }
}