<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews;

use Qualiteam\SkinActMain\AModule;

class Module extends AModule
{
    public static function getYotpoAncoreName(): string
    {
        return '#yotpoReviewsWidget';
    }
}