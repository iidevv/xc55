<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Sale\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Table extends \XLite\View\Form\ItemsList\ProductSelection\Table
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'sale_discount_product_selections';
    }
}
