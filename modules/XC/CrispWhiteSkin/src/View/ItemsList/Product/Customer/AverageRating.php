<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CrispWhiteSkin\View\ItemsList\Product\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Reviews list widget
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
abstract class AverageRating extends \XC\Reviews\View\Customer\ProductInfo\ItemsList\AverageRating
{
    /**
     * Get a list of JS files required to display the widget properly
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/product/items_list/list-rating.js';

        return $list;
    }

    /**
     * Return reviews link label
     *
     * @return string
     */
    protected function getReviewsLinkLabel()
    {
        return static::t('Add first review');
    }

    /**
     * Define whether to display the votes on the page
     *
     * @param \XLite\Model\Product $product
     *
     * @return boolean
     */
    public function isVisibleAddReviewLink($product = null)
    {

        return $this->isAllowedAddReview($product) && !$this->getReviewsCount();
    }

    /**
     * Return average rating for the current product
     *
     * @return integer
     */
    public function getAverageRating()
    {
        return number_format(parent::getAverageRating(), 1, '.', '') ;
    }
}
