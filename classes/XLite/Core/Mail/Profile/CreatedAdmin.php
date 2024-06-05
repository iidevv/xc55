<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Mail\Profile;

class CreatedAdmin extends AProfile
{
    public static function getZone()
    {
        return \XLite::ZONE_ADMIN;
    }

    public static function getDir()
    {
        return 'profile_created';
    }
}
