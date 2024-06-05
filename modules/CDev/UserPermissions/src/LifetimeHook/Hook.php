<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\UserPermissions\LifetimeHook;

final class Hook
{
    public function onRebuild(): void
    {
        $enabledRole = \XLite\Core\Database::getRepo('XLite\Model\Role')->findOneBy(['enabled' => true]);
        if (!$enabledRole) {
            $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->getPermanentRole();
            if (!$permanent) {
                $permanent = \XLite\Core\Database::getRepo('XLite\Model\Role')->findFrame(0, 1);
                $permanent = 0 < count($permanent) ? array_shift($permanent) : null;
            }

            if ($permanent) {
                $permanent->setEnabled(true);
            }
        }
    }
}
