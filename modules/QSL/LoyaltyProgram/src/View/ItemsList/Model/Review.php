<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\View\ItemsList\Model;

use XCart\Extender\Mapping\Extender;
use XC\Reviews\Model\Review as ReviewModel;

/**
 * Decorated widget displaying the Edit Reviews table in the back-end.
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\View\ItemsList\Model\Review
{
    /**
     * Delete selected reviews.
     *
     * @return integer
     */
    protected function processRemove()
    {
        $loyaltyProgram = \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance();

        $repo = $this->getRepository();

        foreach ($this->getEntityIdListForRemove() as $id) {
            $review = $repo->find($id);

            if ($review && $review->getProfile()) {
                $wasEligibleForReward = ($review->getStatus() === ReviewModel::STATUS_APPROVED)
                    && ($review->getRating() >= $loyaltyProgram->getMinEligibleProductRating());

                if ($wasEligibleForReward) {
                    // Cancel reward for the review
                    $loyaltyProgram->cancelRewardForReview($review->getProfile(), !$review->getReview());
                }
            }
        }

        return parent::processRemove();
    }
}
