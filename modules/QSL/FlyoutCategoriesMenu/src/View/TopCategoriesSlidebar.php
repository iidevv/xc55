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
class TopCategoriesSlidebar extends \XLite\View\TopCategoriesSlidebar
{
    /**
     * @param  array $categoryDTO
     *
     * @return array
     */
    protected function preprocessDTO($categoryDTO)
    {
        $categoryDTO = parent::preprocessDTO($categoryDTO);

        if ($this->isShowCatIcon()) {
            $categoryDTO['image'] = $categoryDTO['image_id']
                ? \XLite\Core\Database::getRepo('XLite\Model\Image\Category\Image')->find($categoryDTO['image_id'])
                : null;
        }

        return $categoryDTO;
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
     * @return boolean
     */
    protected function isShowCatIcon()
    {
        return \XLite\Core\Config::getInstance()->QSL->FlyoutCategoriesMenu->fcm_show_icons;
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/FlyoutCategoriesMenu/categories/tree';
    }

    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return $this->getDir() . LC_DS . 'body.twig';
    }

    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getDir() . '/ajax-categories.less';

        return $list;
    }

    /**
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = $this->getDir() . '/ajax-categories.js';

        return $list;
    }

    /**
     * @param int   $index
     * @param int   $count
     * @param array $category
     *
     * @return string
     */
    protected function assembleItemClassName($index, $count, $category)
    {
        $classes = parent::assembleItemClassName($index, $count, $category);

        if (
            $category['hasSubcategories']
            && !$this->isAllowedDepthWithoutAjax($category['depth'])
        ) {
            $classes .= ' ajax-category category-id-' . $category['id'];
        }

        return $classes;
    }
}
