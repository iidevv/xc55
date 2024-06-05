<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVaultReadonlyQty\Module\XC\ProductVariants\View\FormField\Inline\Input\Text;

use Qualiteam\SkinActSkuVaultReadonlyQty\View\QtyTooltip;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend("XC\ProductVariants")
 */
class Amount extends \XC\ProductVariants\View\FormField\Inline\Input\Text\Amount
{
    /**
     * Define fields
     *
     * @return array
     */
    protected function defineFields()
    {
        $fields = parent::defineFields();

        $product = $this->getEntity()->getProduct();

        if ($product && !$product->isSkippedFromSync()) {
            $fields['qty_tooltip'] = [
                'name'  => 'qty_tooltip',
                'class' => QtyTooltip::class,
            ];
        }

        return $fields;
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/Qualiteam/SkinActSkuVaultReadonlyQty/variants/style.css';

        return $list;
    }
}
