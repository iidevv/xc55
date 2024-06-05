<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\View\StickyPanel\ItemsList;

use XC\Reviews\View\Button\ItemsExport\ProductReviews;

class ProductReview extends Review
{
    /**
     * Define buttons widgets
     *
     * @return array
     */
    protected function defineButtons()
    {
        $list = parent::defineButtons();
        $list['export'] = $this->getWidget([], ProductReviews::class);

        return $list;
    }
}
