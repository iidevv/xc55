<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Admin;

use XCart\Extender\Mapping\Extender;
use QSL\LoyaltyProgram\Logic\LoyaltyProgram;
use XC\Reviews\Model\Review as ReviewModel;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class Review extends \XC\Reviews\Controller\Admin\Review
{
    protected $loyaltyProgramPreviousStatus;

    /**
     * Delete a review.
     */
    public function doActionDelete()
    {
        $review = $this->getReview();

        if ($review && $review->getProfile()) {
            $loyaltyProgram = \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance();

            $wasEligibleForReward = ($review->getStatus() === ReviewModel::STATUS_APPROVED)
                && ($review->getRating() >= $loyaltyProgram->getMinEligibleProductRating());

            if ($wasEligibleForReward) {
                // Cancel reward for the review
                $loyaltyProgram->cancelRewardForReview($review->getProfile(), !$review->getReview());
            }
        }

        parent::doActionDelete();
    }

    /**
     * Modify model
     */
    protected function doActionModify()
    {
        // Save information on the review to decide whether the customer should be rewarded for the review,
        // or a prior reward for the review should be cancelled.
        $review             = $this->getModelForm()->getModelObject();
        $previousRating     = $review ? $review->getRating() : null;
        $previousStatus     = $this->loyaltyProgramPreviousStatus ?? ($review ? $review->getStatus() : null);
        $previousReviewText = $review ? $review->getReview() : '';

        parent::doActionModify();

        $review = $this->getModelForm()->getModelObject();

        $updated = LoyaltyProgram::getInstance()->updateRewardForReview(
            $previousStatus,
            $previousRating,
            $previousReviewText,
            $review->getProfile(),
            $review
        );

        if ($updated) {
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Approve review
     */
    protected function doActionApprove()
    {
        $this->loyaltyProgramPreviousStatus = $this->getModelForm()->getModelObject()->getStatus();

        parent::doActionApprove();
    }
}
