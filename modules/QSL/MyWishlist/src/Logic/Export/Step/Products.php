<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\Logic\Export\Step;

use XCart\Extender\Mapping\Extender;

/**
 * Products
 * @Extender\Mixin
 */
abstract class Products extends \XLite\Logic\Export\Step\Products
{
    /**
     * We do not export snapshot products
     *
     * @param \XLite\Model\AEntity $model
     *
     * @return void
     */
    protected function processModel(\XLite\Model\AEntity $model)
    {
        if (!$model->isSnapshotProduct()) {
            parent::processModel($model);
        }
    }
}
