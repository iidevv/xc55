<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\View;

/**
 * Wholesale prices for product
 */
class ProductPriceQuicklook extends \CDev\Wholesale\View\ProductPrice
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/Wholesale/product_price_quicklook/body.twig';
    }
}
