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
 * @Extender\Depend({"XC\SystemFields", "XC\ProductVariants"})
 */
class ProductVariantsUpcIsbn extends \XC\GoogleFeed\Logic\Feed\Step\Products
{
    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantMpn(\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getDisplayMnfVendor() ?: parent::getVariantMpn($model);
    }

    /**
     * @param \XC\ProductVariants\Model\ProductVariant $model
     * @return string
     */
    protected function getVariantGtin(\XC\ProductVariants\Model\ProductVariant $model)
    {
        return $model->getDisplayUpcIsbn() ?: parent::getVariantGtin($model);
    }
}
