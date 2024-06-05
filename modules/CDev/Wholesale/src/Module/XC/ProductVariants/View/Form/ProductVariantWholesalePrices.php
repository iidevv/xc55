<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\Module\XC\ProductVariants\View\Form;

use XCart\Extender\Mapping\Extender;

/**
 * WholesalePrices form
 *
 * @Extender\Depend("XC\ProductVariants")
 */
class ProductVariantWholesalePrices extends \CDev\Wholesale\View\Form\WholesalePrices
{
    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $list = [];

        $list['page'] = $this->page;
        $list['id'] = $this->getProductVariant()->getId();

        return $list;
    }
}
