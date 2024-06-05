<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\View\FormModel\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Info extends \XLite\View\FormModel\Product\Info
{
    protected function defineFields()
    {
        $schema = parent::defineFields();

        $product = $this->getProductEntity();
        if ($product && !$product->isSkippedFromSync()) {
            $schema['prices_and_inventory']['inventory_tracking']['help'] =
                static::t('Quantity is read-only because it is controlled by SkuVault');
        }

        return $schema;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVaultReadonlyQty/product/style.css';

        return $list;
    }
}
