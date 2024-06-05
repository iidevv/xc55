<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\BackInStock\View\Form\ItemsList\Product;

/**
 * Products list search form
 */
class Search extends \XLite\View\Form\AForm
{
    /**
     * @inheritdoc
     */
    protected function getDefaultTarget()
    {
        return 'back_in_stock_products';
    }

    /**
     * @inheritdoc
     */
    protected function getDefaultAction()
    {
        return 'search';
    }
}
