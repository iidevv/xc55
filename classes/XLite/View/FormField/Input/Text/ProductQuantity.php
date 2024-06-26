<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\FormField\Input\Text;

/**
 * Integer
 */
class ProductQuantity extends \XLite\View\FormField\Input\Text\Integer
{
    /**
     * Return field template
     *
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'input/text/product_quantity.twig';
    }
}
