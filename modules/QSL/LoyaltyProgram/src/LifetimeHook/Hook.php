<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace QSL\LoyaltyProgram\LifetimeHook;

use CDev\SimpleCMS\Model\Menu;
use QSL\LoyaltyProgram\Main;

final class Hook
{
    public function onRebuild(): void
    {
        if (class_exists(Menu::class)) {
            Main::addSimpleCMSMenuLink();
        }

        \XLite\Core\Database::getEM()->flush();
    }
}
