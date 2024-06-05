<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;

class SkuvaultOrders extends SkuVault
{
    const OPTIONS = [
        'skuvault_orders_enable_sync',
        'skuvault_orders_from_id',
    ];
}
