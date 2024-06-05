<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\ProductAdvisor\Module\XC\ProductComparison\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductComparison")
 */
class ComparisonTable extends \XC\ProductComparison\View\ComparisonTable
{
    protected function getProductButtonWidget(\XLite\Model\Product $product)
    {
        return $product->isUpcomingProduct()
            ? $this->getWidget([], '\CDev\ProductAdvisor\View\Label\ComingSoonLabel')
            : parent::getProductButtonWidget($product);
    }
}
