<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\Product;

use XCart\Extender\Mapping\ListChild;

/**
 * Reviews tab on product details page
 *
 * @ListChild (list="product.details.page.tab.reviews")
 */
class ReviewsTab extends \XLite\View\AView
{
    /**
     * Define product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return \XLite\Core\Database::getRepo('XLite\Model\Product')->find($this->getProductId());
    }

    /**
     * Get a list of CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.css';
        $list[] = 'modules/XC/Reviews/review/style.css';

        return $list;
    }

    /**
     * Get a list of JS files
     *
     * @return array
     */
    public function getJSFiles()
    {
        $list = parent::getJSFiles();

        $list[] = 'modules/XC/Reviews/form_field/input/rating/rating.js';

        return $list;
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/XC/Reviews/reviews_tab/body.twig';
    }

    /**
     * Is "Read all reviews for the product" link visible
     *
     * @return bool
     */
    protected function isDisplayReadAllReviewsLink()
    {
        return $this->getProduct() && $this->getProduct()->getReviewsCount() > $this->getPageReviewsCount();
    }

    /**
     * Check if show fixed count of reviews on product page, otherwise pager will be used
     *
     * @return bool
     */
    protected function getPageReviewsCount()
    {
        return \XLite\Core\Config::getInstance()->XC->Reviews->reviewsCountPerTab;
    }
}
