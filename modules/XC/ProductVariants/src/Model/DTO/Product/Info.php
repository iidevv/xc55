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
class Info extends \XLite\Model\DTO\Product\Info
{
    /**
     * @inheritdoc
     */
    protected static function isSKUValid($dto)
    {
        if (parent::isSKUValid($dto)) {
            $sku = $dto->default->sku;
            return !\XLite\Core\Database::getRepo('XC\ProductVariants\Model\ProductVariant')->findOneBySku($sku);
        }

        return false;
    }

    protected function init($object)
    {
        parent::init($object);

        $this->prices_and_inventory->inventory_tracking->clear_variants_inventory = false;
    }

    public function populateTo($object, $rawData = null)
    {
        parent::populateTo($object, $rawData);

        if ($this->prices_and_inventory->inventory_tracking->clear_variants_inventory) {
            /** @var \XC\ProductVariants\Model\ProductVariant $variant */
            foreach ($object->getVariants() as $variant) {
                $variant->setDefaultAmount(true);
            }
        }
    }
}
