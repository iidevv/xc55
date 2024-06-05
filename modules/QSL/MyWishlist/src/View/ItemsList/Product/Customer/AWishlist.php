<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\ItemsList\Product\Customer;

/**
 * Wishlist for customer area abstract class
 */
abstract class AWishlist extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    protected function getProductWidgetClass()
    {
        return 'QSL\MyWishlist\View\Product\MyWishlistListItem';
    }

    /**
     * Return target to retrive this widget from AJAX
     *
     * @return string
     */
    protected static function getWidgetTarget()
    {
        return 'wishlist';
    }

    /**
     * Returns CSS classes for the container element
     *
     * @return string
     */
    public function getListCSSClasses()
    {
        return parent::getListCSSClasses() . ' wishlist-products';
    }

    /**
     * Initialize widget (set attributes)
     *
     * @param array $params Widget params
     *
     * @return void
     */
    public function setWidgetParams(array $params)
    {
        parent::setWidgetParams($params);

        unset($this->widgetParams[self::PARAM_SHOW_SORT_BY_SELECTOR]);
    }

    /**
     * Get title
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Wishlist';
    }

    /**
     * Return class name for the list pager
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return 'XLite\View\Pager\Customer\Product\Search';
    }

    /**
     * Return params list to use for search
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchConditions(\XLite\Core\CommonCell $cnd)
    {
        return $cnd;
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return mixed
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        $result = \XLite\Core\Database::getRepo('QSL\MyWishlist\Model\WishlistLink')->getWishlistProducts($this->getWishlist(), $countOnly);

        return $countOnly
            ? $result
            : array_map(
                [$this, 'retrieveProduct'],
                $this->getCndResult($result, $cnd)
            );
    }

    protected function getCndResult($result, $cnd)
    {
        if ($cnd->{\XLite\Model\Repo\Product::P_LIMIT}) {
            $value = $cnd->{\XLite\Model\Repo\Product::P_LIMIT};
            $offset = $value[0];
            $length = $value[1];

            $result = array_slice($result, $offset, $length);
        }

        return $result;
    }

    /**
     * We retrieve the parent product if there is one
     *
     * @param \QSL\MyWishlist\Model\WishlistLink $elem Product element from the search query
     *
     * @return \XLite\Model\Product
     */
    public function retrieveProduct(\QSL\MyWishlist\Model\WishlistLink $elem)
    {
        $parentProduct = $elem->getParentProduct();

        $product = ($parentProduct && $parentProduct->isVisible())
            ? $parentProduct
            : $elem->getSnapshotProduct();

        $product->setWishlistLinkId($elem->getId());

        return $product;
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-wishlist-products';
    }

    /**
     * Get product list item widget params required for the widget of type getProductWidgetClass().
     *
     * @param \XLite\Model\Product $product
     *
     * @return array
     */
    protected function getProductWidgetParams(\XLite\Model\Product $product)
    {
        return parent::getProductWidgetParams($product) + [
            'wishlist_product'  => $product,
        ];
    }
}
