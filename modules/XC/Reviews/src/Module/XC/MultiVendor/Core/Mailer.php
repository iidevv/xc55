<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Module\XC\MultiVendor\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XC\Reviews\Core\Mail\NewReviewVendor;

/**
 * Mailer
 *
 * @Extender\Mixin
 * @Extender\Depend ({"XC\Reviews","XC\MultiVendor"})
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    /**
     * @param \XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReview(\XC\Reviews\Model\Review $review)
    {
        if ($review->getProduct()->getVendor()) {
            static::sendNewReviewVendor(
                $review->getProduct()->getVendor(),
                $review
            );
        } else {
            static::sendNewReviewAdmin($review);
        }
    }

    /**
     * @param \XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReviewVendor(\XLite\Model\Profile $vendor, \XC\Reviews\Model\Review $review)
    {
        static::getBus()->dispatch(new SendMail(NewReviewVendor::class, [$review, $vendor]));
    }

    /**
     * @param \XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReviewAdmin(\XC\Reviews\Model\Review $review)
    {
        parent::sendNewReview($review);
    }
}
