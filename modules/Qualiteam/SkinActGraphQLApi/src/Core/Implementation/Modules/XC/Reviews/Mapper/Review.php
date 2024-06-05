<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */
namespace Qualiteam\SkinActGraphQLApi\Core\Implementation\Modules\XC\Reviews\Mapper;

class Review
{
    /**
     * @param \XC\Reviews\Model\Review $review
     *
     * @return array
     */
    public function mapToArray(\XC\Reviews\Model\Review $review)
    {
        return [
            'id'            => $review->getId(),
            'review'        => $review->getReview(),
            'reviewerName'  => $review->getReviewerName(),
            'rating'        => $review->getRating(),
            'additionDate'  => \XLite\Core\Converter::formatTime($review->getAdditionDate()),
        ];
    }
}