<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class Review extends \XC\Reviews\Controller\Customer\Review
{
    /**
     * Rating of the review before the update.
     *
     * @var integer
     */
    protected $previousRating;

    /**
     * Review status before the update.
     *
     * @var integer
     */
    protected $previousStatus;

    /**
     * Whether the model had only the rating before the update, or the rating and the review.
     *
     * @var string
     */
    protected $previousText;

    /**
     * Modify model
     */
    protected function doActionModify()
    {
        // Save information on the review to decide whether the customer should be rewarded for the review,
        // or a prior reward for the review should be cancelled.
        $this->saveOldReviewData();

        parent::doActionModify();

        $this->giveReviewRewards();
    }

    /**
     * Save the previous review data.
     */
    protected function saveOldReviewData()
    {
        if ($this->getId()) {
            $review               = $this->getReview();
            $this->previousRating = $review->getRating();
            $this->previousStatus = $review->getStatus();
            $this->previousText   = $review->getReview();
        }
    }

    /**
     * Do necessary actions with the review that has just been created/updated.
     */
    protected function giveReviewRewards()
    {
        $review  = $this->getReview();
        \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance()->updateRewardForReview(
            $this->previousStatus,
            $this->previousRating,
            $this->previousText,
            $review->getProfile(),
            $review
        );

        \XLite\Core\Database::getEM()->flush();
    }
}
