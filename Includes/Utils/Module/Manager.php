<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes\Utils\Module;

class Manager
{
    private static ?Registry $registry = null;

    public static function getRegistry(): Registry
    {
        if (self::$registry === null) {
            self::$registry = new Registry();
        }

        return self::$registry;
    }
}
