<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FeaturedProducts\View\Form\ItemsList\ProductSelection;

/**
 * Product selections list table form
 */
class Search extends \XLite\View\Form\ItemsList\ProductSelection\Search
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'f_product_selections';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getCommonFormParams()
    {
        $list = parent::getCommonFormParams();
        $list['currentCategoryID']
            = \XLite\Core\Request::getInstance()->categoryId ?: $this->getRootCategoryId();

        return $list;
    }
}
