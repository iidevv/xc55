<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomerAttachments\View\Product;

use XCart\Extender\Mapping\Extender;

/**
 * Product list item widget
 * @Extender\Mixin
 */
class ListItem extends \XLite\View\Product\ListItem
{
    /**
     * Return class attribute for the product cell
     *
     * @return object
     */
    public function getProductCellClass()
    {
        $class = parent::getProductCellClass();

        $class .= $this->getProduct()->isCustomerAttachmentsMandatory() ? ' attachment-required' : '';

        return $this->getSafeValue($class);
    }

    /**
     * Link should redirect to product page instead of adding to cart if attachment is required
     *
     * @return boolean
     */
    protected function isGotoProduct()
    {
        return parent::isGotoProduct()
            || $this->getProduct()->isCustomerAttachmentsMandatory();
    }
}
