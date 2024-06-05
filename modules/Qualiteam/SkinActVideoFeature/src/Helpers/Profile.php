<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Helpers;

use Qualiteam\SkinActProMembership\Helpers\Profile as ProfileHelper;

class Profile
{
    public static function isProMembership()
    {
        return (new ProfileHelper)->isProfileProMembership();
    }
}