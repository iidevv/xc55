<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Segment\Core;

use XCart\Extender\Mapping\Extender;

/**
 * Event mediator
 *
 * @Extender\Mixin
 * @Extender\Depend ("XC\Reviews")
 */
class MediatorReviews extends \QSL\Segment\Core\Mediator
{
    /**
     * 'track(Rated Product)' event
     *
     * @param \XC\Reviews\Model\Review $review Review
     */
    public function doRatedProduct(\XC\Reviews\Model\Review $review)
    {
        if ($this->isTrackAllowed('Rated_Product')) {
            $this->addMessage(
                'track',
                $this->assembleRateProductMessage($review),
                (bool)$review->getReview()
            );
        }
    }

    /**
     * 'track(Reviewed Product)' event
     *
     * @param \XC\Reviews\Model\Review $review Review
     */
    public function doReviewedProduct(\XC\Reviews\Model\Review $review)
    {
        if ($this->isTrackAllowed('Reviewed_Product')) {
            $this->addMessage(
                'track',
                $this->assembleReviewedProductMessage($review),
                true
            );
        }
    }

    /**
     * Assemble message for 'track (rated product)' request
     *
     * @param \XC\Reviews\Model\Review $review Review
     *
     * @return array
     */
    protected function assembleRateProductMessage(\XC\Reviews\Model\Review $review)
    {
        /** @var \XLite\Model\Product $product */
        $product = $review->getProduct();

        return [
            'event'      => 'Rated Product',
            'properties' => [
                'id'       => $product->getProductId(),
                'sku'      => $product->getSku(),
                'name'     => $product->getName(),
                'price'    => $product->getPrice(),
                'category' => $product->getCategory()->getStringPath(),
                'rate'     => $review->getRating(),
            ],
        ];
    }

    /**
     * Assemble message for 'track (reviewed product)' request
     *
     * @param \XC\Reviews\Model\Review $review Review
     *
     * @return array
     */
    protected function assembleReviewedProductMessage(\XC\Reviews\Model\Review $review)
    {
        /** @var \XLite\Model\Product $product */
        $product = $review->getProduct();

        return [
            'event'      => 'Reviewed Product',
            'properties' => [
                'id'       => $product->getProductId(),
                'sku'      => $product->getSku(),
                'name'     => $product->getName(),
                'price'    => $product->getPrice(),
                'category' => $product->getCategory()->getStringPath(),
                'reviewer' => $review->getReviewerName(),
                'email'    => $review->getEmail(),
                'review'   => $review->getReview(),
            ],
        ];
    }
}
