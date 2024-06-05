<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\Product;

/**
 * Product list item widget
 */
class MyWishlistListItem extends \XLite\View\Product\ListItem
{
    protected function addWishlistLabels($labels)
    {
        return $labels;
    }

    protected function getProduct()
    {
        return $this->getParam('wishlist_product') ?: parent::getProduct();
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            'wishlist_product' => new \XLite\Model\WidgetParam\TypeObject('Wishlist product'),
        ];
    }

    /**
     * Get product URL
     *
     * @param integer $categoryId Category ID
     *
     * @return string
     */
    protected function getProductURL($categoryId = null)
    {
        $product = $this->getProduct();

        return $product->isSnapshotProduct()
            ? $this->getSnapshotProductURL($product)
            : parent::getProductURL($categoryId);
    }

    protected function getSnapshotProductURL(\XLite\Model\Product $product)
    {
        return $this->buildURL('product', '', [
            'wishlist_link_id'  => $product->getWishlistLinkId(),
            'mode'              => 'wishlist_link',
        ]);
    }

    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        $params[] = $this->getProduct()->getWishlistLinkId();

        return $params;
    }
}
