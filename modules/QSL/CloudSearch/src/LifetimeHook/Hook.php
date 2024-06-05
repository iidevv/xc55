<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\CloudSearch\LifetimeHook;

use QSL\CloudSearch\Core\RegistrationScheduler;

final class Hook
{
    public function onRebuild(): void
    {
        RegistrationScheduler::getInstance()->schedule();
    }
}
