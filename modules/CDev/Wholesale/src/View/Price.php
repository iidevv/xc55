<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Price extends \XLite\View\Price
{
    /**
     * Widget parameter names
     */
    public const PARAM_QUANTITY = 'quantity';

    /**
     * Return JS files for widget
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list   = parent::getJSFiles();
        $list[] = 'modules/CDev/Wholesale/wholesale_product_page.js';

        return $list;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        $product = parent::getProduct();
        $product->setWholesaleQuantity($this->getParam(static::PARAM_QUANTITY));

        return $product;
    }

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_QUANTITY => new \XLite\Model\WidgetParam\TypeInt('Product quantity', 1),
        ];
    }
}
