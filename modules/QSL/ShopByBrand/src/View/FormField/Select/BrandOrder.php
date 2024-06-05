<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\FormField\Select;

use QSL\ShopByBrand\Model\Repo\Brand as Repo;

/**
 * Form field to select the way how brands should be ordered on the Brands page.
 */
class BrandOrder extends \XLite\View\FormField\Select\Regular
{
    /**
     * Returns default options list
     *
     * @return array
     */
    protected function getDefaultOptions()
    {
        $list = [
            Repo::SORT_BY_BRAND_NAME    => static::t('by brand names'),
            Repo::SORT_BY_ADMIN_DEFINED => static::t('as configured by the store owner'),
            Repo::SORT_BY_PRODUCT_COUNT => static::t('by number of products'),
        ];

        return $list;
    }
}
