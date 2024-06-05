<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\Controller\Admin;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class Reviews extends \XC\Reviews\Controller\Admin\Reviews
{
    /**
     * Do action 'approve'
     */
    protected function doActionApprove()
    {
        parent::doActionApprove();
    }

    /**
     * Do action 'unapprove'
     */
    protected function doActionUnapprove()
    {
        parent::doActionUnapprove();
    }

    /**
     * Do action 'delete'
     */
    protected function doActionDelete()
    {
        $data = $this->getPreviousSettingsForSelected();

        parent::doActionDelete();

        if (!empty($data)) {
            $this->updateRewardsForReviews($data);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Change statuses of the reviews from request
     *
     * @param integer $status New status
     */
    protected function changeReviewStatuses($status)
    {
        $data = $this->getPreviousSettingsForSelected();

        parent::changeReviewStatuses($status);

        if (!empty($data)) {
            $this->updateRewardsForReviews($data);
            \XLite\Core\Database::getEM()->flush();
        }
    }

    /**
     * Retrieves the list of submitted reviews and returns information on them.
     *
     * @return array
     */
    protected function getPreviousSettingsForSelected()
    {
        $data = [];

        $select = \XLite\Core\Request::getInstance()->select;
        if ($select && is_array($select)) {
            $repo = \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review');
            foreach ($repo->findByIds(array_keys($select)) as $review) {
                $data[$review->getId()] = [
                    'status'  => $review->getStatus(),
                    'text'    => $review->getReview(),
                    'rating'  => intval($review->getRating()),
                    'profile' => $review->getProfile(),
                    'review'  => $review,
                ];
            }
        }

        return $data;
    }

    /**
     * Goes through an array with information on past reviews' data and gives or
     * discards rewards for ratings and reviews depending on the old and the new
     * review status.
     *
     * @param array $data Past review data
     */
    protected function updateRewardsForReviews(array $data)
    {
        $loyaltyProgram = \QSL\LoyaltyProgram\Logic\LoyaltyProgram::getInstance();

        foreach ($data as $id => $info) {
            $loyaltyProgram->updateRewardForReview(
                $info['status'],
                $info['rating'],
                $info['text'],
                $info['profile'],
                $info['review']
            );
        }
    }
}
