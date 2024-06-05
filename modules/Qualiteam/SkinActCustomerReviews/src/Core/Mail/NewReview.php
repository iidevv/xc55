<?php

/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Core\Mail;

use XCart\Extender\Mapping\Extender;
use XC\Reviews\Model\Review;

/**
 * @Extender\Mixin
 */
class NewReview extends \XC\Reviews\Core\Mail\NewReview
{
    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'review_title' => '',
            'review_advantages' => '',
            'review_disadvantages' => '',
        ]);
    }

    public function __construct(Review $review)
    {
        parent::__construct($review);

        $this->populateVariables([
            'review_title' => $review->getTitle(),
            'review_advantages' => $review->getAdvantages(),
            'review_disadvantages' => $review->getDisadvantages(),
        ]);

    }
}