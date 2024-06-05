<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\SpecialOffersBase\View\ItemsList\Promotions;

use XCart\Extender\Mapping\ListChild;

/**
 * Special offers promoted on category pages.
 *
 * @ListChild (list="center.bottom", zone="customer", weight="105")
 */
class ProductOffers extends \QSL\SpecialOffersBase\View\ItemsList\Promotions\APromotedOffers
{
    /**
     * Widget parameters
     */
    public const PARAM_CATEGORY    = 'product';
    public const PARAM_CATEGORY_ID = 'product_id';

    /**
     * Cached category entity that we are displaying offers for.
     *
     * @var \XLite\Model\Product
     */
    protected $product;

    /**
     * Return list of targets allowed for this widget
     *
     * @return array
     */
    public static function getAllowedTargets()
    {
        $result = parent::getAllowedTargets();
        $result[] = 'product';

        return $result;
    }

    /**
     * Return the specific widget service name to make it visible as specific CSS class.
     *
     * @return string
     */
    public function getFingerprint()
    {
        return parent::getFingerprint() . '-product';
    }

    /**
     * Register the CSS classes for this block
     *
     * @return string
     */
    protected function getBlockClasses()
    {
        return parent::getBlockClasses() . ' block-promoted-offers-product';
    }
}
