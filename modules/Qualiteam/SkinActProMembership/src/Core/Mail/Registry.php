<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\Core\Mail;

use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Registry extends \XLite\Core\Mail\Registry
{
    protected static function getNotificationsList()
    {
        return array_merge_recursive(parent::getNotificationsList(), [
            \XLite::ZONE_ADMIN => [
                'modules/Qualiteam/SkinActProMembership/pro_membership' => BuyProMembership::class,
                'modules/Qualiteam/SkinActProMembership/pro_membership_expiration_reminder' => ProMembershipExpirationReminder::class,
            ],
            \XLite::ZONE_CUSTOMER => [
                'modules/Qualiteam/SkinActProMembership/pro_membership_expiration_reminder' => ProMembershipExpirationReminder::class,
            ],
        ]);
    }
}