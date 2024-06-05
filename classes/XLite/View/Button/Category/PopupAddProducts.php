<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Button\Category;

use XLite\Model\WidgetParam\TypeString;
use XLite\View\Button\PopupProductSelector;

/**
 * Product selection in popup
 */
class PopupAddProducts extends PopupProductSelector
{
    public const PARAM_ID = 'id';

    /**
     * Defines the target of the product selector
     * The main reason is to get the title for the selector from the controller
     *
     * @return string
     */
    protected function getSelectorTarget()
    {
        return 'category_product_selections';
    }

    /**
     * Adds the necessary CSS/LESS files to the list.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [ 'category/add_products.less' ]
        );
    }

    /**
     * Defines the class name of the widget which will display the product list dialog
     *
     * @return string
     */
    protected function getSelectorViewClass()
    {
        return 'XLite\View\Category\PopupAddProductsSelector';
    }

    /**
     * Return URL parameters to use in AJAX popup
     *
     * @return array
     */
    protected function prepareURLParams()
    {
        return array_merge(
            parent::prepareURLParams(),
            [
                static::PARAM_ID => $this->getParam(static::PARAM_ID),
            ]
        );
    }

    /**
     * Define widget params
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ID => new TypeString('Category id, if it is provided', ''),
        ];
    }

    /**
     * Return CSS classes
     *
     * @return string
     */
    protected function getClass()
    {
        return 'btn regular-button popup-product-category-selection';
    }

    /**
     * getDefaultLabel
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return 'Add products';
    }

    /**
     * Defines the list of additional JS files that should be included.
     *
     * @return array
     */
    public function getJSFiles()
    {
        return array_merge(
            parent::getJSFiles(),
            [ 'category/add_products.js' ]
        );
    }
}
