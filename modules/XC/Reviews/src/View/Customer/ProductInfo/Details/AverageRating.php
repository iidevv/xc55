<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Customer\ProductInfo\Details;

/**
 * Reviews list widget
 *
 */
class AverageRating extends \XC\Reviews\View\AverageRating
{
    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/product/details/rating.twig';
    }
}
