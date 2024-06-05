<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XC\GDPR\LifetimeHook;

use XC\GDPR\Core\Activity;
use XC\GDPR\Core\PrivacyPolicy;

final class Hook
{
    public function onRebuild(): void
    {
        if (PrivacyPolicy::getInstance()->isNeedToCreateStaticPage()) {
            PrivacyPolicy::getInstance()->createPrivacyPolicyStaticPage();
        }

        Activity::updateAllActivities();
    }
}
