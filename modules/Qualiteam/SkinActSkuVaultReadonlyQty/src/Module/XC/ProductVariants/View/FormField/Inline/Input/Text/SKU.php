<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\ProductVariants\View\FormField\Inline\Input\Text;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class SKU extends \XC\ProductVariants\View\FormField\Inline\Input\Text\SKU
{
    /**
     * Get initial field parameters
     *
     * @param array $field Field data
     *
     * @return array
     */
    protected function getFieldParams(array $field)
    {
        $product = $this->getEntity()->getProduct();

        $required = $product && !$product->isSkippedFromSync() ? ['required' => true] : [];

        return parent::getFieldParams($field) + $required;
    }
}
