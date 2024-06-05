<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\MembershipProducts\Core\Mail;

class AMail extends \XLite\Core\Mail\AMail
{
    public static function getZone()
    {
        return \XLite::ZONE_CUSTOMER;
    }

    public static function getDir()
    {
        return static::MESSAGE_DIR;
    }

    protected static function defineVariables()
    {
        return array_merge(parent::defineVariables(), [
            'membership_name' => static::t('Membership'),
        ]);
    }
}
