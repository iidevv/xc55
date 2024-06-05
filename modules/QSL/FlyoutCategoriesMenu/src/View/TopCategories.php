<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\FlyoutCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class TopCategories extends \XLite\View\TopCategories
{
    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/FlyoutCategoriesMenu/';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . 'ajax-categories.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . 'ajax-categories.js';

        return $list;
    }

    /**
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams[static::PARAM_DISPLAY_MODE]->setValue(static::DISPLAY_MODE_TREE);
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function postprocessDTOs($categories)
    {
        $categories = parent::postprocessDTOs($categories);

        if (\XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num) {
            foreach ($categories as $categoryDTO) {
                $tmpParent = $categories[$categoryDTO['parent_id']] ?? null;

                $productsCount = $categoryDTO['productsCount'];
                while ($tmpParent) {
                    $categories[$tmpParent['id']]['productsCount'] += $productsCount;
                    $tmpParent = $categories[$tmpParent['parent_id']] ?? null;
                }
            }
        }

        return $categories;
    }

    /**
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $classes = [];

        $active = $this->isActiveTrail($category['id']);

        if (!$category['hasSubcategories']) {
            $classes[] = 'leaf';
        }

        if ($index == 0) {
            $classes[] = 'first';
        }

        if (($count - 1) == $index) {
            $classes[] = 'last';
        }

        if ($active) {
            $classes[] = 'active-trail';
        }

        if (
            $category['hasSubcategories']
            && !$this->isAllowedDepthWithoutAjax($category['depth'])
        ) {
            $classes[] = 'ajax-category';
            $classes[] = 'category-id-' . $category['id'];
        }

        return implode(' ', $classes);
    }

    /**
     * @param integer   $index    Item number
     * @param integer   $count    Items count
     * @param array     $category Current category
     *
     * @return string
     */
    protected function assembleLinkClassName($index, $count, $category)
    {
        $classes = [];

        $classes[] = \XLite\Core\Request::getInstance()->category_id == $category['id']
            ? 'active'
            : '';

        $classes[] = $this->isWordWrapDisabled() ? 'no-wrap' : '';

        return implode(' ', $classes);
    }

    /**
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-flyout-categories-menu';
    }

    /**
     * @return boolean
     */
    protected function isShowProductNum()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_product_num;
    }

    /**
     * Check if display subcategory triangle
     *
     * @return boolean
     */
    protected function isShowTriangle()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_triangle;
    }

    /**
     * Check if word wrap disabled
     *
     * @return boolean
     */
    protected function isWordWrapDisabled()
    {
        return !\XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_word_wrap;
    }
}
