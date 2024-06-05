<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActXPaymentsSubscriptions\Controller\Admin;

use XLite\Core\Database;
use XLite\Core\Request;
use XLite\Model\Profile;

/**
 * Subscriptions list for user controller
 * todo: rename to XPaymentsProfileSubscription
 */
class XPaymentsUserSubscription extends XPaymentsSubscription
{
    /**
     * getProfileId
     *
     * @return integer
     */
    public function getProfileId()
    {
        return Request::getInstance()->profile_id;
    }

    /**
     * Get profile
     *
     * @return Profile
     */
    public function getProfile()
    {
        return Database::getRepo(Profile::class)
            ->find($this->getProfileId());
    }
}
