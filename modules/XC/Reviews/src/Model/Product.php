<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Model;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Product reviews
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\Reviews\Model\Review", mappedBy="product", cascade={"all"})
     * @ORM\OrderBy   ({"additionDate" = "DESC"})
     */
    protected $reviews;

    /**
     * Votes count (run-time cache)
     *
     * @var integer
     */
    protected $votesCount = null;

    /**
     * Reviews count (run-time cache)
     *
     * @var integer
     */
    protected $reviewsCount = null;

    /**
     * Average rating (run-time cache)
     *
     * @var float
     */
    protected $averageRating = null;

    /**
     * Return count of votes
     *
     * @return integer
     */
    public function getVotesCount()
    {
        if (!isset($this->votesCount)) {
            $cnd = $this->getConditions();
            $countOnly = true;
            $this->votesCount = \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review')->search($cnd, $countOnly);
        }

        return $this->votesCount;
    }

    /**
     * Return product reviews count
     *
     * @return integer
     */
    public function getReviewsCount()
    {
        if (!isset($this->reviewsCount)) {
            $cnd = $this->getConditions();
            $cnd->{\XC\Reviews\Model\Repo\Review::SEARCH_TYPE}
                = \XC\Reviews\Model\Repo\Review::SEARCH_TYPE_REVIEWS_ONLY;
            $countOnly = true;
            $this->reviewsCount = \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review')->search($cnd, $countOnly);
        }

        return $this->reviewsCount;
    }

    /**
     * Return product average rating
     *
     * @return float
     */
    public function getAverageRating()
    {
        if (!isset($this->averageRating)) {
            $cnd = $this->getConditions();

            $avg = \XLite\Core\Database::getRepo('\XC\Reviews\Model\Review')->search(
                $cnd,
                \XC\Reviews\Model\Repo\Review::SEARCH_MODE_AVG
            );

            $this->averageRating = $avg !== null
                ? number_format($avg, 2)
                : 0;
        }

        return $this->averageRating;
    }

    /**
     * Return ratings distortion
     *
     * @return array
     */
    public function getRatings()
    {
        $maxRating = $this->getMaxRatingValue();

        $status = \XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews
            ? \XC\Reviews\Model\Review::STATUS_APPROVED
            : null;
        $votes = \XLite\Core\Database::getRepo('XC\Reviews\Model\Review')->getVotesCount($this, $status);

        $result = [];

        if ($votes) {
            $totalCount = array_sum($votes);
            for ($rating = $maxRating; 0 < $rating; $rating--) {
                $count    = $votes[$rating] ?? 0;
                $percent  = ceil(100 * $count / $totalCount);
                $result[] = [
                    'count'                 => $count,
                    'percent'               => $percent,
                    'rating'                => $rating,
                    'showPercentLastDiv'    => 98 > $percent,
                ];
            }
        }

        return $result;
    }

    /**
     * Define whether product was rated somewhere or not
     *
     * @return boolean
     */
    public function isEmptyAverageRating()
    {
        return 0 >= $this->getAverageRating();
    }

    /**
     * Return maximum allowable rating value
     *
     * @return integer
     */
    public function getMaxRatingValue()
    {
        return \XC\Reviews\Model\Review::MAX_RATING;
    }

    /**
     * Return review added by customer
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return \XC\Reviews\Model\Review
     */
    public function getReviewAddedByUser(\XLite\Model\Profile $profile = null)
    {
        $review = $this->executeCachedRuntime(function () use ($profile) {
            $review = null;

            if ($profile) {
                $review = \XLite\Core\Database::getRepo('XC\Reviews\Model\Review')->findOneBy(
                    [
                        'product' => $this,
                        'profile' => $profile,
                    ]
                );
            }

            return $review ?: false;
        }, ['getReviewAddedByUser', $profile ? $profile->getProfileId() : '']);

        return $review ?: null;
    }

    /**
     * Return TRUE if customer already rated product
     *
     * @param \XLite\Model\Profile $profile Profile
     *
     * @return boolean
     */
    public function isRatedByUser(\XLite\Model\Profile $profile = null)
    {
        $review = $this->getReviewAddedByUser($profile);

        return ($review != null);
    }

    /**
     * Return TRUE if customer already added review for the product
     *
     * @param \XLite\Model\Profile $profile
     *
     * @return bool
     */
    public function isReviewedByUser(\XLite\Model\Profile $profile = null)
    {
        $review = $this->getReviewAddedByUser($profile);

        return ($review != null && $review->getReview());
    }

    /**
     * Get conditions
     *
     * @return array
     */
    protected function getConditions()
    {
        $cnd = new \XLite\Core\CommonCell();

        $cnd->{\XC\Reviews\Model\Repo\Review::SEARCH_PRODUCT} = $this;

        if (\XLite\Core\Config::getInstance()->XC->Reviews->disablePendingReviews == true) {
            $cnd->{\XC\Reviews\Model\Repo\Review::SEARCH_STATUS}
                = \XC\Reviews\Model\Review::STATUS_APPROVED;
        }

        return $cnd;
    }

    /**
     * Add reviews
     *
     * @param \XC\Reviews\Model\Review $reviews
     * @return Product
     */
    public function addReviews(\XC\Reviews\Model\Review $reviews)
    {
        $this->reviews[] = $reviews;

        if ($profile = $reviews->getProfile()) {
            $this->setRuntimeCache(['getReviewAddedByUser', $profile->getProfileId()], null);
        }

        if (!$reviews->isPersistent()) {
            $reviews->sendNotificationToOwner();
        }

        return $this;
    }

    /**
     * Get reviews
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReviews()
    {
        return $this->reviews;
    }
}
