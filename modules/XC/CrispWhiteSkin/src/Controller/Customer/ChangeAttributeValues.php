<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Change attribute values from cart / wishlist item
 * @Extender\Mixin
 */
class ChangeAttributeValues extends \XLite\Controller\Customer\ChangeAttributeValues
{
    /**
     * Get page title
     *
     * @return string
     */
    public function getTitle()
    {
        return '';
    }

    /**
     * Get page title
     *
     * @return string
     */
    public function getProductTitle()
    {
        return parent::getTitle();
    }
}
