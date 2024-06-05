<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkin\View\Product;

use XCart\Extender\Mapping\Extender;
use XLite\View\Button\Simple;

/**
 * @Extender\Mixin
 */
abstract class ListItem extends \XLite\View\Product\ListItem
{
    protected function getAdd2CartBlockWidget()
    {
        $product = $this->getProduct();

        if ($product->isOutOfStock() || $product->isAllStockInCart()) {
            return $this->getWidget(
                [
                    'style' => 'out-of-stock',
                    'label' => 'Out of stock',
                ],
                Simple::class
            );
        }

        return parent::getAdd2CartBlockWidget();
    }

    /**
     * Cache availability
     *
     * @return boolean
     */
    protected function isCacheAvailable()
    {
        return false;
    }
}
