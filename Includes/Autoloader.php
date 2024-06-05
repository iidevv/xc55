<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Includes;

/**
 * Autoloader
 * @deprecated
 */
abstract class Autoloader
{
    /**
     * Register generated autoloader for PSR-4 compliant classes
     *
     * @param  string $namespace Root namespace
     * @param  string $path      Absolute path to folder, where classes are placed
     */
    public static function registerCustom($namespace, $path)
    {
        spl_autoload_register(
            static function ($class) use ($namespace, $path) {
                $class = ltrim($class, '\\');

                if (strpos($class, $namespace) === 0) {
                    include_once($path . '//../' . str_replace('\\', LC_DS, $class) . '.php');
                }
            }
        );
    }
}
