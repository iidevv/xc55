<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ShopByBrand\View\Button\ItemsExport;

/**
 * Products in certain category ItemsExport button
 */
class BrandProducts extends \XLite\View\Button\ItemsExport\CategoryProduct
{
    protected function getAdditionalButtons()
    {
        $list = parent::getAdditionalButtons();
        $sessionParam = \QSL\ShopByBrand\View\ItemsList\Model\Product\Admin\BrandProducts::getConditionCellName();
        foreach ($list as $key => $btn) {
            $btn->getWidgetParams('session')->setValue($sessionParam);
        }

        return $list;
    }
}
