<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Category;

use XLite\Core\Request;
use XLite\View\Form\ItemsList\ProductSelection\Search;

/**
 * Product selections list table form
 */
class AddProducts extends Search
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'category_product_selections';
    }

    /**
     * Adds the category ID to the list of the form params.
     *
     * @return array
     */
    protected function getFormParams()
    {
        $result       = parent::getFormParams();
        $result['id'] = (int) Request::getInstance()->id;

        return $result;
    }
}
