<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Request;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    /**
     * If brand_id GET parameter is set, add it to the form action link.
     *
     * @return array
     */
    protected function getActionParams()
    {
        $result = parent::getActionParams();
        $brandId = (int) Request::getInstance()->brand_id;
        if ($brandId) {
            $result['brand_id'] = $brandId;
        }
        return $result;
    }
}
