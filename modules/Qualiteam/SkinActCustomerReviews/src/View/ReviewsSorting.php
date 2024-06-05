<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View;

use XCart\Extender\Mapping\ListChild;
use XLite\Core\Request;

/**
 * @ListChild (list="product.reviews.page", zone="customer", weight="199")
 */
class ReviewsSorting extends \XLite\View\AView
{
    protected function getUsefulSortLink()
    {
        $params = [
            'product_id' => Request::getInstance()->product_id,
            'order_useful' => 1
        ];

        $url = $this->buildURL('product_reviews', '', $params);

        return $url;
    }

    protected function getDateSortLink()
    {
        $params = [
            'product_id' => Request::getInstance()->product_id,
            'order_date' => 1
        ];

        $url = $this->buildURL('product_reviews', '', $params);

        return $url;
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReviewsSorting.twig';
    }
}