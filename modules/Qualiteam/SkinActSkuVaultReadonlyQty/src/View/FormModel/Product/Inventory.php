<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Database;
use XLite\Model\Product;

/**
 * @Extender\Mixin
 */
class Inventory extends \XLite\View\FormModel\Product\Inventory
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $productId = $this->getDataObject()->default->identity;
        $product = Database::getRepo(Product::class)->find($productId);

        if ($product && !$product->isSkippedFromSync()) {
            $schema[self::SECTION_DEFAULT]['quantity_in_stock']['disabled'] = true;
            $schema[self::SECTION_DEFAULT]['quantity_in_stock']['help']     =
                static::t('Quantity is read-only because it is controlled by SkuVault');
        }

        return $schema;
    }
}
