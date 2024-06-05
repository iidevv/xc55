<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Menu\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
abstract class AAdmin extends \XLite\View\Menu\Admin\AAdmin
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->relatedTargets['product_list'][] = 'back_in_stock_products';
        $this->relatedTargets['product_list'][] = 'back_in_stock_product_prices';

        $this->relatedTargets['back_in_stock_records'][] = 'back_in_stock_record_prices';

        parent::__construct($params);
    }
}
