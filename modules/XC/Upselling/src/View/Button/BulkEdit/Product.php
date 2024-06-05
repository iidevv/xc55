<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Upselling\View\Button\BulkEdit;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\BulkEditing")
 */
class Product extends \XC\BulkEditing\View\Button\Product
{
    /**
     * @return array
     */
    protected function getScenarios()
    {
        $result = parent::getScenarios();
        // $result['product_related_products'] = [
        //     'position' => 900,
        // ];

        return $result;
    }
}
