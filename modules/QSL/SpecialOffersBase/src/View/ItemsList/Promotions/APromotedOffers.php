<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\ItemsList\Promotions;

use QSL\SpecialOffersBase\Model\SpecialOffer as SpecialOfferModel;
use QSL\SpecialOffersBase\View\Promo\SpecialOffer;
use XLite\Core\Auth;
use XLite\View\Pager\Infinity;

/**
 * Widget displaying an abstract list of special offers promoted on the page.
 */
class APromotedOffers extends \XLite\View\ItemsList\AItemsList
{
    /**
     * Rows of data.
     *
     * @var array
     */
    protected $rows;

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return 'widget-fingerprint-promoted-offers';
    }

    /**
     * Register CSS files required by the widget.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getDir() . '/' . $this->getPageBodyDir() . '/styles.css';

        return $list;
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
    protected function getPageBodyDir()
    {
        return 'promoted_offers';
    }

    /**
     * Get widget templates directory
     * NOTE: do not use "$this" pointer here (see "getBody()" and "get[CSS/JS]Files()")
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/SpecialOffersBase/items_list';
    }

    /**
     * Returns the sorted list of available booking options.
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition
     * @param boolean                $countOnly Whether to return items, or just the number of them OPTIONAL
     *
     * @return array|integer
     */
    protected function getData(\XLite\Core\CommonCell $cnd, $countOnly = false)
    {
        if ($this->isWidgetEnabled()) {
            return $this->getRepo()->search($cnd, $countOnly);
        }

        return $countOnly ? 0 : [];
    }

    /**
     * Dependent modules should enable this flag to get the widget displayed.
     *
     * @return boolean
     */
    protected function isWidgetEnabled()
    {
        return false;
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return $this->isWidgetEnabled() && parent::isVisible();
    }

    /**
     * Returns parameters to filter the list of available booking options.
     *
     * @return \XLite\Core\CommonCell
     */
    protected function getSearchCondition()
    {
        return $this->getRepo()->getActiveOffersConditions(Auth::getInstance()->getProfile());
    }

    /**
     * Returns repository object for special offers model.
     *
     * @return \QSL\SpecialOffersBase\Model\Repo\SpecialOffer
     */
    protected function getRepo()
    {
        return \XLite\Core\Database::getRepo(SpecialOfferModel::class);
    }

    /**
     * Returns block title.
     *
     * @return string
     */
    protected function getHead()
    {
        return 'Special offers';
    }

    /**
     * Auxiliary method to check visibility
     *
     * @return boolean
     */
    protected function isDisplayWithEmptyList()
    {
        return false;
    }

    /**
     * Returns the widget class name.
     *
     * @return string
     */
    protected function getWidgetClassname()
    {
        return SpecialOffer::class;
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
    public function getRowCSSClass($row)
    {
        if (!$row) {
            $class = 'first';
        } elseif ($row === $this->countRows() - 1) {
            $class = 'last';
        } else {
            $class = '';
        }

        return $class;
    }

    /**
     * Get CSS class for the row tag.
     *
     * @param integer $row    Row index.
     * @param integer $column Column index.
     *
     * @return string
     */
    public function getColumnCSSClass($row, $column)
    {
        if (!$column) {
            $class = 'first';
        } elseif ($column === $this->getColumnsCount() - 1) {
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
    public function getItemInlineStyle()
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
    public function getImageWidth()
    {
        return 160;
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMinItemWidth()
    {
        return ($this->getImageWidth() + 70) . 'px';
    }

    /**
     * Return the minimum width of an item in the grid.
     *
     * @return string
     */
    public function getMaxItemWidth()
    {
        return (($this->getImageWidth() + 70) * 2) . 'px';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers';
    }
}
