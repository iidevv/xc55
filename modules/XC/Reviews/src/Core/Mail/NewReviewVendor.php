<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Core\Mail;

use XLite\Core\Mailer;
use XLite\Model\Profile;
use XC\Reviews\Model\Review;

class NewReviewVendor extends NewReview
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return Mailer::NEW_REVIEW_NOTIFICATION;
    }

    public function __construct(Review $review, Profile $vendor)
    {
        parent::__construct($review);
        $this->setTo(['email' => $vendor->getLogin(), 'name' => $vendor->getName(false)]);
    }
}
