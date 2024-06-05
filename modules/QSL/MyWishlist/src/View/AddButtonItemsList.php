<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MyWishlist\View;

use XCart\Extender\Mapping\ListChild;

/**
 * @ListChild (list="itemsList.product.grid.customer.info", weight="0")
 * @ListChild (list="itemsList.product.list.customer.info", weight="1100")
 * @ListChild (list="itemsList.product.table.customer.columns", weight="90")
 */
class AddButtonItemsList extends \XLite\View\AView
{
    /**
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/MyWishlist/items_list/product/parts/common.add-button.twig';
    }
}
