<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\Controller\Admin;

class BackInStockProducts extends \QSL\BackInStock\Controller\Admin\ABackInStockProducts
{
    /**
     * @return string
     */
    public function getItemsListClass()
    {
        return parent::getItemsListClass() ?: 'QSL\BackInStock\View\ItemsList\Model\Product';
    }
}
