<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\HorizontalCategoriesMenu\Module\XC\MultiVendor\View;

use XCart\Extender\Mapping\Extender;
use XC\MultiVendor\Logic;

/**
 * Sidebar categories list
 *
 * @Extender\Mixin
 * @Extender\Depend("XC\MultiVendor")
 */
class TopCategoriesSlidebarHorizontal extends \XLite\View\TopCategoriesSlidebar
{
    /**
     * @inheritdoc
     */
    protected function getCacheParameters()
    {
        $params = parent::getCacheParameters();

        if (Logic\Vendors::getVendorSpecificMode()) {
            $params[] = Logic\Vendors::getVendorSpecificMode();
        }

        return $params;
    }

    /**
     * Get cache parameters for preprocessed DTOs
     *
     * @return array
     */
    protected function getProcessedDTOsCacheParameters()
    {
        $cacheParameters = parent::getProcessedDTOsCacheParameters();

        if (Logic\Vendors::getVendorSpecificMode()) {
            $cacheParameters[] = Logic\Vendors::getVendorSpecificMode();
        }

        return $cacheParameters;
    }

    /**
     * Preprocess DTO
     *
     * @param array $tree
     *
     * @return array
     */
    protected function postprocessDTOs($tree)
    {
        $tree = parent::postprocessDTOs($tree);

        if (Logic\Vendors::getVendorSpecificMode()) {
            if (!\XLite\Core\Config::getInstance()->QSL->HorizontalCategoriesMenu->hfcm_show_product_num) {
                foreach ($tree as $categoryDTO) {
                    $tmpParent = $tree[$categoryDTO['parent_id']] ?? null;

                    $productsCount = $categoryDTO['productsCount'];
                    while ($tmpParent) {
                        $tree[$tmpParent['id']]['productsCount'] += $productsCount;
                        $tmpParent = $tree[$tmpParent['parent_id']] ?? null;
                    }
                }
            }

            // Remove nodes with zero productsCount
            $tree = array_filter($tree, static function ($item) {
                return $item['productsCount'] > 0;
            });
        }

        return $tree;
    }
}
