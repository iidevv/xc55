<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCreateOrder\View;


class ProductPrice extends \CDev\Wholesale\View\ProductPrice
{
    protected function getCart()
    {
        return $this->cart;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCreateOrder/product_price/body.twig';
    }
}