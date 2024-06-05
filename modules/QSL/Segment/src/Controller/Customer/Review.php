<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Controller\Customer;

use XCart\Extender\Mapping\Extender;

/**
 * Review controller
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class Review extends \XC\Reviews\Controller\Customer\Review
{
    /**
     * Last edited review
     *
     * @var \XC\Reviews\Model\Review
     */
    protected $last_review;

    /**
     * @inheritdoc
     */
    protected function doActionModify()
    {
        $old = null;
        $old_rating = null;
        $old_review = null;
        if ($this->getId()) {
            $old = $this->getReview();
            if ($old) {
                $old_rating = $old->getRating();
                $old_review = $old->getReview();
            }
        }

        parent::doActionModify();

        if ($this->last_review) {
            $data = $this->getRequestData();

            if (!empty($data['rating']) && (!$old || $old_rating != $data['rating'])) {
                \QSL\Segment\Core\Mediator::getInstance()->doRatedProduct($this->last_review);
            }
            if (!empty($data['review']) && (!$old || $old_review != $data['review'])) {
                \QSL\Segment\Core\Mediator::getInstance()->doReviewedProduct($this->last_review);
            }
        }
    }

    /**
     * Update review ids saved in session
     * used for connection between anonymous user and his reviews
     *
     * @param \XC\Reviews\Model\Review $entity Entity
     *
     * @return bool
     */
    protected function updateReviewIds(\XC\Reviews\Model\Review $entity)
    {
        $this->last_review = $entity;

        return parent::updateReviewIds($entity);
    }
}
