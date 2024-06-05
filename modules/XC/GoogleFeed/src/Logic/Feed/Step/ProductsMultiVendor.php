<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\GoogleFeed\Logic\Feed\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products step
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class ProductsMultiVendor extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    /**
     * @param \XC\MultiVendor\Model\Product $model
     * @return string
     */
    protected function getRecordId(\XLite\Model\Product $model)
    {
        return $model->getVendor()
            ? $model->getVendorId() . '-' . parent::getRecordId($model)
            : parent::getRecordId($model);
    }
}
