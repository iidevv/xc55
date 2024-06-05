<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Core;

use XCart\Extender\Mapping\Extender;
use XCart\Messenger\Message\SendMail;
use XC\Reviews\Core\Mail\NewReview;
use XC\Reviews\Core\Mail\OrderReviewKey;

/**
 * Mailer
 * @Extender\Mixin
 */
abstract class Mailer extends \XLite\Core\Mailer
{
    public const NEW_REVIEW_NOTIFICATION     = 'modules/XC/Reviews/new_review';
    public const NEW_REVIEW_KEY_NOTIFICATION = 'modules/XC/Reviews/review_key';

    /**
     * @param \XC\Reviews\Model\Review $review Review
     */
    public static function sendNewReview(\XC\Reviews\Model\Review $review)
    {
        static::getBus()->dispatch(new SendMail(NewReview::class, [$review]));
    }

    /**
     * Send order review key (follow up notification) to customer
     *
     * @param \XC\Reviews\Model\OrderReviewKey $reviewKey Review key object
     */
    public static function sendOrderReviewKey(\XC\Reviews\Model\OrderReviewKey $reviewKey)
    {
        static::getBus()->dispatch(new SendMail(OrderReviewKey::class, [$reviewKey]));
    }
}
