<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Product\Details\Customer\Page;

use XCart\Extender\Mapping\ListChild;
use XLite\Model\Cart;

/**
 * Main
 *
 * @ListChild (list="center", zone="customer")
 */
class Main extends \XLite\View\Product\Details\Customer\Page\APage
{
    /**
     * Return list of allowed targets
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();
        $list[] = 'product';

        return $list;
    }

    /**
     * getDir
     *
     * @return string
     */
    protected function getDir()
    {
        return parent::getDir() . '/page';
    }

    /**
     * Get container attributes
     *
     * @return array
     */
    protected function getContainerAttributes()
    {
        $collection = new \XLite\View\ProductPageCollection(['product' => $this->getProduct()]);
        $collection = $collection->getWidgetsCollection();
        $productId = $this->getProduct()->getProductId();
        $classes = [
            'product-details',
            'product-info-' . $productId,
            'box-product',
        ];
        if (Cart::getInstance()->isProductAdded($productId)) {
            $classes[] = 'product-added';
        }

        return [
            'class'                       => $classes,
            'data-use-widgets-collection' => !empty($collection),
        ];
    }
}
