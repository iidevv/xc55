<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Menu\Admin;

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
        $this->relatedTargets['brands'][] = 'brand';
        $this->relatedTargets['brands'][] = 'brand_products';

        parent::__construct($params);
    }
}
