<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\StickyPanel\ItemsList;

/**
 * Search product list sticky panel
 */
class BrandProducts extends \XLite\View\StickyPanel\Product\Admin\CategoryProducts
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget(
            [],
            'QSL\ShopByBrand\View\Button\ItemsExport\BrandProducts'
        );
        return $list;
    }
}
