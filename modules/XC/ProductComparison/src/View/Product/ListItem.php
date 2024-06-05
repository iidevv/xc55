<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductComparison\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Check - if .buttons-container is present
     *
     * @return boolean
     */
    protected function hasButtons()
    {
        return true;
    }

    /**
     * Get cache parameters
     *
     * @return array
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $productIds = \XC\ProductComparison\Core\Data::getInstance()->getProductIds();

        $params[] = in_array($this->getProductId(), $productIds);

        return $params;
    }
}
