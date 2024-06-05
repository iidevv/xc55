<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\MarketPrice\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Return product labels
     *
     * @return array
     */
    protected function getLabels()
    {
        $product = $this->getProduct();

        $label = \CDev\MarketPrice\Core\Labels::getLabel($product);

        if ($product->getMarketPrice() && !$label) {
            $widget = $this->getWidget(
                [
                    'product'   => $product,
                ],
                'XLite\View\Price'
            );
            $widget->getMarketPriceLabel();
        }

        return parent::getLabels() + \CDev\MarketPrice\Core\Labels::getLabel($product);
    }
}
