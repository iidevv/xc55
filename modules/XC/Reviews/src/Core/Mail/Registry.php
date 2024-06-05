<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Registry extends \XLite\Core\Mail\Registry
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_CUSTOMER => [
                'modules/XC/Reviews/review_key' => OrderReviewKey::class,
            ],
            \XLite::ZONE_ADMIN    => [
                'modules/XC/Reviews/new_review' => NewReview::class,
            ],
        ]);
    }
}
