<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Product\Details;

/**
 * Widget promoting reward points which customers will earn after purchasing the product.
 *
 * Widget requires the "product" parameter that should be passed from a parent widget.
 * That's why we use ListChild in an extra template that just inserts this widget.
 */
class ProductPoints extends \QSL\LoyaltyProgram\View\Product\AProductPoints
{
    /**
     * Get filename of the promo template.
     *
     * @return string
     */
    protected function getPromoTemplateName()
    {
        return 'product_points.details.twig';
    }
}
