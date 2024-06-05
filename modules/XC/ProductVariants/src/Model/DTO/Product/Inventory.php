<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\Model\DTO\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Inventory extends \XLite\Model\DTO\Product\Inventory
{
    protected function init($object)
    {
        parent::init($object);

        $this->default->clear_variants_inventory = false;
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        if ($this->default->clear_variants_inventory) {
            /** @var \XC\ProductVariants\Model\ProductVariant $variant */
            foreach ($object->getVariants() as $variant) {
                $variant->setDefaultAmount(true);
            }
        }
    }
}
