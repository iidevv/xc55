<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Sitemap\View\Sitemap;

use XCart\Extender\Mapping\Extender;

/**
 *  This widget draws a tree's branch
 *
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Sale")
 */
class BranchSale extends \XC\Sitemap\View\Sitemap\Branch
{
    /**
     * Get children
     *
     * @param string  $type Page type
     * @param integer $id   Page ID
     *
     * @return array
     */
    protected function getChildren($type, $id)
    {
        $result = parent::getChildren($type, $id);

        if ($type == static::PAGE_CATEGORY && $id == \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategoryId()) {
            array_push($result, [
                'type' => static::PAGE_STATIC,
                'id'   => '997',
                'name' => static::t('Sale'),
                'url'  => static::buildURL('sale_products'),
            ]);
        }

        return $result;
    }
}
