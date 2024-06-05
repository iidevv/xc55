<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\Product\ItemsList;

/**
 * Widget promoting reward points which customers will earn after purchasing the product in a list.
 */
class ProductPoints extends \QSL\LoyaltyProgram\View\Product\AProductPoints
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/QSL/LoyaltyProgram/promo/product_points.css';

        return $list;
    }

    /**
     * Get filename of the promo template.
     *
     * @return string
     */
    protected function getPromoTemplateName()
    {
        return 'product_points.itemsList.twig';
    }
}
