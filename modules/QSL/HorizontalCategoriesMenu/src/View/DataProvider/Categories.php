<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View\DataProvider;

trait Categories
{
    /**
     * Collect categories collection
     *
     * @return array
     */
    protected function collectCategories()
    {
        $cacheKey = md5(serialize($this->getProcessedDTOsCacheParameters()));
        $driver   = \XLite\Core\Database::getCacheDriver();

        if ($driver->contains($cacheKey)) {
            return $driver->fetch($cacheKey);
        }

        $preprocessedDTOs = [];

        $excludeRootCategory = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type === 'root_categories';
        $dtos                = \XLite\Core\Database::getRepo('XLite\Model\Category')->getCategoriesAsDTO($excludeRootCategory);
        foreach ($dtos as $key => $categoryDTO) {
            $preprocessedDTOs[$categoryDTO['id']] = $this->preprocessDTO($categoryDTO);
        }

        $postprocessedDTOs = $this->postprocessDTOs($preprocessedDTOs);
        $driver->save($cacheKey, $postprocessedDTOs);

        return $postprocessedDTOs;
    }

    /**
     * @param array $categories
     *
     * @return array
     */
    protected function postprocessDTOs($categories)
    {
        if (\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_product_num) {
            foreach ($categories as $categoryDTO) {
                $tmpParent = $categories[$categoryDTO['parent_id']] ?? null;

                $productsNum = $categoryDTO['productsNum'];
                while ($tmpParent) {
                    $categories[$tmpParent['id']]['productsNum'] += $productsNum;
                    $tmpParent                                   = $categories[$tmpParent['parent_id']] ?? null;
                }
            }
        }

        if (\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type === 'catalog') {
            foreach ($categories as $k => $categoryDTO) {
                // move categories (including the root one) to one level down
                $categories[$k]['depth']++;
            }
        }

        return $categories;
    }

    /**
     * Return subcategories list
     *
     * @param null $categoryId Category id OPTIONAL
     *
     * @return array
     */
    protected function getCategories($categoryId = null)
    {
        if ($this->categories === null) {
            $this->categories = $this->collectCategories();
        }

        if (!$categoryId) {
            if (\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type === 'catalog') {
                $rootCategory = array_values(array_filter($this->categories, static function ($item) {
                    return !isset($item['parent_id']);
                })) ?: [];
                if ($rootCategory) {
                    $rootCategory[0]['link'] = \XLite::getInstance()->getShopURL();
                    $rootCategory[0]['name'] = static::t('Catalog one-root menu name');
                }

                return $rootCategory;
            }

            $categoryId = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId();
        }

        return array_filter($this->categories, static function ($item) use ($categoryId) {
            return isset($item['parent_id']) && (int)$item['parent_id'] === (int)$categoryId;
        });
    }
}
