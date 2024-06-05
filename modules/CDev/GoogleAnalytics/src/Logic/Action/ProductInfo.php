<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\GoogleAnalytics\Logic\Action;

use CDev\GoogleAnalytics\Logic\Action;

class ProductInfo extends Action\Base\AProduct
{
    protected static function getActionType(): string
    {
        return 'addProduct';
    }
}
