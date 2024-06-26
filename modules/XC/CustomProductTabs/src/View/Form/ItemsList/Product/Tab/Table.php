<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\View\Form\ItemsList\Product\Tab;

/**
 * Product tabs list table form
 */
class Table extends \XLite\View\Form\ItemsList\AItemsList
{
    /**
     * Return default value for the "target" parameter
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'product';
    }

    /**
     * Return default value for the "action" parameter
     *
     * @return string
     */
    protected function getDefaultAction()
    {
        return 'updateProductTabs';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */

    protected function getDefaultParams()
    {
        return [
            'tab_id' => \XLite\Core\Request::getInstance()->tab_id,
            'product_id' => \XLite\Core\Request::getInstance()->product_id,
            'page' => \XLite\Core\Request::getInstance()->page,
        ];
    }
}
