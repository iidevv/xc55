<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFreeGifts\View\ItemsList\FreeGift;

use Qualiteam\SkinActFreeGifts\Model\FreeGiftItem;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Config;
use XLite\Core\Database;
use XLite\Model\Cart;
use XLite\Model\Product;
use XLite\Model\Repo\ARepo;
use XLite\View\Pager\Infinity;

/**
 * Widget to display gift list
 *
 * @ListChild (list="center.bottom", zone="customer", weight="1000")
 */
class FreeGift extends \XLite\View\ItemsList\Product\Customer\ACustomer
{
    /**
     * @var array
     */
    protected array $freeGifts = [];

    /**
     * Rows of data.
     *
     * @var array
     */
    protected $rows;

    protected function getAddToCartLabel($product)
    {
        /** @var \XLite\Model\Cart $cart */
        $cart = $this->getCart();

        if ($cart->isEmpty()) {
            return static::t('Add to cart');
        }

        /** @var \XLite\Model\OrderItem $item */
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() && $item->getProduct() === $product) {
                return static::t('Added');
            }
        }

        return static::t('Add to cart');
    }

    public static function getAllowedTargets()
    {
        return [
            'cart',
        ];
    }

    protected function getSortBy()
    {
        return null;
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [
                'modules/Qualiteam/SkinActFreeGifts/cart/parts/cart_view.js',
                'modules/Qualiteam/SkinActFreeGifts/cart/controller.js',
                'modules/Qualiteam/SkinActFreeGifts/cart/elements.js',
            ]
        );
    }

    /**
     * Get a list of JavaScript files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/Qualiteam/SkinActFreeGifts/gift.less',
            ],
        );
    }

    /**
     * Return default template
     * See setWidgetParams()
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActFreeGifts/items_list/free_gifts/body.twig';
    }

    /**
     * Returns class name for the list pager.
     *
     * @return string
     */
    protected function getPagerClass()
    {
        return Infinity::class;
    }

    /**
     * Returns path to the directory where the page body template resides in
     *
     * @return string
     */
    //protected function getPageBodyDir()
    //{
    //    return 'free_gifts';
    //}

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    //protected function getDir()
    //{
    //    return 'modules/Qualiteam/SkinActFreeGifts/items_list';
    //}

    /**
     * @return array
     */
    public function getFreeGiftProductsList(): array
    {
        if (empty($this->freeGifts)) {
            $giftTierId = $this->getCart()->getFreeGiftTier()?->getGiftTierId();

            $freeGifts = $this->getRepository()->getGiftTierProducts($giftTierId);

            $this->freeGifts = array_map([$this, "prepareFreeGiftProductsList"], $freeGifts);
        }

        return $this->freeGifts;
    }

    /**
     * @return bool
     */
    public function hasFreeGiftProductsList(): bool
    {
        return (bool)$this->getFreeGiftProductsList();
    }

    /**
     * @param \Qualiteam\SkinActFreeGifts\Model\FreeGiftItem $item
     *
     * @return Product
     */
    protected function prepareFreeGiftProductsList(FreeGiftItem $item): Product
    {
        return $item->getProduct();
    }

    /**
     * Dependent modules should enable this flag to get the widget displayed.
     *
     * @return boolean
     */
    protected function isWidgetEnabled()
    {
        return true;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->isWidgetEnabled()
            && !$this->isCartEmpty();
    }

    /**
     * @return bool
     */
    protected function isCartEmpty(): bool
    {
        return $this->getCart()->isEmpty();
    }

    /**
     * Returns parameters to filter the list of available booking options.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        $cnd = parent::getSearchCondition();

        if (!$this->isCartEmpty()) {
            $cnd->cart_subtotal = $this->getCart()->getFreeGiftsSubtotal();
        }

        return $cnd;
    }

    /**
     * @return bool
     */
    protected function hasFreeGiftTier(): bool
    {
        return (bool)$this->getCart()->getFreeGiftTier();
    }

    /**
     * Returns repository object for special offers model.
     *
     * @return \XLite\Model\Repo\ARepo
     */
    protected function getRepository(): ARepo
    {
        return Database::getRepo(FreeGiftItem::class);
    }

    /**
     * Define repository name
     *
     * @return string
     */
    protected function defineRepositoryName(): string
    {
        return FreeGiftItem::class;
    }

    /**
     * Returns the number of columns in the list.
     *
     * @return integer
     */
    protected function getColumnsCount()
    {
        return 3;
    }

    /**
     * Get rows of data.
     *
     * @return array
     */
    public function getRows()
    {
        if (!isset($this->rows)) {
            $this->rows = array_chunk($this->getPageData(), $this->getColumnsCount());
        }

        return $this->rows;
    }

    /**
     * Count the total number of rows.
     *
     * @return integer
     */
    public function countRows()
    {
        return isset($this->rows) ? count($this->rows) : count($this->getRows());
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param integer $row Row index.
     *
     * @return string
     */
    public function getRowCSSClass($row): string
    {
        if (!$row) {
            $class = 'first';
        } elseif ($row == $this->countRows() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param integer $row Row index.
     * @param integer $column Column index.
     *
     * @return string
     */
    public function getColumnCSSClass($row, $column): string
    {
        if (!$column) {
            $class = 'first';
        } elseif ($column == $this->getColumnsCount() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Returns the inline CSS for an item in the grid.
     *
     * @return string
     */
    public function getItemInlineStyle(): string
    {
        $items = [];

        $min = $this->getMinItemWidth();
        if ($min) {
            $items[] = "min-width: {$min}";
        }

        $max = $this->getMaxItemWidth();
        if ($max) {
            $items[] = "max-width: {$max}";
        }

        return implode('; ', $items);
    }

    /**
     * Brand logo width.
     *
     * @return integer
     */
    public function getImageWidth(): int
    {
        return 160;
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMinItemWidth(): string
    {
        return ($this->getImageWidth() + 70) . 'px';
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMaxItemWidth(): string
    {
        return (($this->getImageWidth() + 70) * 2) . 'px';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses(): string
    {
        return parent::getBlockClasses() . ' block-free-gifts';
    }

    /**
     * @return string
     */
    public function getFreeGiftTierName(): string
    {
        return $this->getCart()->getFreeGiftTier()?->getTierName();
    }

    protected function isGotoProduct(Product $product)
    {
        return $product->hasEditableAttributes()
            && Config::getInstance()->General->force_choose_product_options !== '';
    }

    public function getCommentedData(): array
    {
        return [
            "widget_class" => $this->getWidgetClass(),
            "widget_target" => 'cart',
            "added-to-cart-lbl" => static::t('SkinActFreeGifts added to cart'),
        ];
    }

    protected function cartAlreadyHasGift(): bool
    {
        return Cart::getInstance()->hasGiftItemAlready();
    }
}
