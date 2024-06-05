<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\View;

use XCart\Extender\Mapping\Extender;

/**
 * Sidebar categories list
 *
 * (list="layout.header.categories", zone="customer", weight="10")
 * @Extender\Mixin
 */
class TopCategoriesSlidebar extends \XLite\View\TopCategoriesSlidebar
{
    use DataProvider\Categories;

    /**
     * Get widget templates directory
     *
     * @return string
     */
    protected function getDir()
    {
        return 'modules/QSL/HorizontalCategoriesMenu/tree';
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/HorizontalCategoriesMenu/tree/body.twig';
    }

    /**
     * Get cache parameters for proprocessed DTOs
     *
     * @return array
     */
    protected function getProcessedDTOsCacheParameters()
    {
        $cacheParameters = parent::getProcessedDTOsCacheParameters();
        $cacheParameters[] = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type;

        return $cacheParameters;
    }

    public function rootId(): ?int
    {
        return $this->getDefaultCategoryId();
    }

    /**
     * ID of the default root category
     */
    protected function getDefaultCategoryId(): ?int
    {
        return \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type === 'catalog' ? null : parent::getDefaultCategoryId();
    }

    /**
     * @return array
     */
    protected function getCacheParameters()
    {
        $list   = parent::getCacheParameters();
        $list[] = \XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_category_menu_type;

        return $list;
    }
}
