<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View\ItemsList\Model\Product\Admin;

/**
 * Wishlist products
 */
class WishlistProducts extends \XLite\View\ItemsList\Model\Product\Admin\Search
{
    /**
     * Get CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $return = parent::getCSSFiles();

        $return[] = 'modules/QSL/MyWishlist/items_list/model/wishlist/style.less';

        return $return;
    }

    /**
     * Returns the list of targets where this widget is available
     *
     * @return string[]
     */
    public static function getAllowedTargets()
    {
        $list = parent::getAllowedTargets();

        $list[] = 'wishlist';

        return $list;
    }

    protected function getSearchPanelClass()
    {
        return null;
    }

    /**
     * Get create entity URL
     *
     * @return string
     */
    protected function getCreateURL()
    {
        return null;
    }

    /**
     * Mark list as sortable
     *
     * @return integer
     */
    protected function getSortableType()
    {
        return static::SORT_TYPE_NONE;
    }

    /**
     * Get search conditions
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = new \XLite\Core\CommonCell();

        return $cnd;
    }

    /**
     * Define columns structure
     *
     * @return array
     */
    protected function defineColumns()
    {
        return [
            'sku' => [
                static::COLUMN_NAME    => static::t('SKU'),
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_TYPE_NONE,
                static::COLUMN_ORDERBY => 100,
            ],
            'name' => [
                static::COLUMN_NAME    => static::t('Name'),
                static::COLUMN_MAIN    => true,
                static::COLUMN_NO_WRAP => true,
                static::COLUMN_SORT    => static::SORT_TYPE_NONE,
                static::COLUMN_ORDERBY => 200,
                static::COLUMN_TEMPLATE => 'modules/QSL/MyWishlist/items_list/model/wishlist/cell.name.twig',
            ],
        ];
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
     * Creation button position
     *
     * @return integer
     */
    protected function isCreation()
    {
        return static::CREATE_INLINE_NONE;
    }

    /**
     * Get list name suffixes
     *
     * @return array
     */
    protected function getListNameSuffixes()
    {
        return array_merge(parent::getListNameSuffixes(), ['wishlist']);
    }

    /**
     * Check - sticky panel is visible or not
     *
     * @return boolean
     */
    protected function isPanelVisible()
    {
        return false;
    }

    /**
     * Get panel class
     *
     * @return \XLite\View\Base\FormStickyPanel
     */
    protected function getPanelClass()
    {
        return 'XLite\View\StickyPanel\ItemsListForm';
    }

    /**
     * Get URL common parameters
     *
     * @return array
     */
    protected function getCommonParams()
    {
        $this->commonParams = parent::getCommonParams();

        $this->commonParams['wishlist_id'] = $this->getWishlistId();
        $this->commonParams['profile_id']  = $this->getProfileId();

        return $this->commonParams;
    }

    // {{{ Behaviors

    /**
     * Mark list as removable
     *
     * @return boolean
     */
    protected function isRemoved()
    {
        return false;
    }

    /**
     * Mark list as switchable (enable / disable)
     *
     * @return boolean
     */
    protected function isSwitchable()
    {
        return false;
    }

    /**
     * Mark list as selectable
     *
     * @return boolean
     */
    protected function isSelectable()
    {
        return false;
    }

    // }}}
}
