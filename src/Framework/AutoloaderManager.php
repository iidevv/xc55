<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Framework;

use Symfony\Component\ErrorHandler\DebugClassLoader;
use XCart\Extender\Autoloader\AutoloaderInterface;

/**
 * This class is used to manage XC autoloader
 *
 * @see \App\Operation\Build\GenerateAutoloader
 */
final class AutoloaderManager
{
    private static ?AutoloaderInterface $productionAutoloader = null;

    private static ?AutoloaderInterface $developmentAutoloader = null;

    private static ?AutoloaderInterface $currentAutoloader = null;

    private static array $moduleAutoloaders = [];

    public static function setProductionAutoloader(AutoloaderInterface $autoloader): void
    {
        self::$productionAutoloader = $autoloader;
    }

    public static function setDevelopmentAutoloader(AutoloaderInterface $autoloader): void
    {
        self::$developmentAutoloader = $autoloader;
    }

    public static function getCurrentAutoloader(): ?AutoloaderInterface
    {
        return self::$currentAutoloader;
    }

    public static function setModuleAutoloaders(array $autoloaders): void
    {
        self::$moduleAutoloaders = $autoloaders;
    }

    public static function registerProduction(): void
    {
        if (self::$productionAutoloader) {
            self::register(self::$productionAutoloader);
        }
    }

    public static function registerDevelopment(): void
    {
        if (self::$developmentAutoloader) {
            self::register(self::$developmentAutoloader);
        }
    }

    public static function registerModuleAutoloaders(): void
    {
        foreach (self::$moduleAutoloaders as $moduleAutoloader) {
            $loader = require_once $moduleAutoloader;
            if ($loader instanceof \Composer\Autoload\ClassLoader) {
                $loader->unregister();
                $loader->register();
            } elseif (is_callable($loader)) {
                call_user_func($loader);
            }
        }
    }

    private static function register(AutoloaderInterface $autoloader): void
    {
        // autoloader is already registered, so it is required to replace it with new one
        // the order of autoloading functions must be preserved
        if (self::$currentAutoloader) {
            $functions = spl_autoload_functions();
            foreach ($functions as $function) {
                spl_autoload_unregister($function);
            }

            foreach ($functions as $function) {
                if (self::isCurrentAutoloader($function)) {
                    // use new autoloader
                    $function = [$autoloader, 'autoload'];
                } elseif (self::isWrappedCurrentAutoloader($function)) {
                    // it is required to wrap autoloader if previous was wrapped too
                    $function = [new DebugClassLoader([$autoloader, 'autoload']), 'loadClass'];
                }

                spl_autoload_register($function);
            }

            self::$currentAutoloader = $autoloader;

            return;
        }

        self::$currentAutoloader = $autoloader;
        self::$currentAutoloader->register();
    }

    private static function isCurrentAutoloader($function): bool
    {
        if (self::$currentAutoloader) {
            return
                is_array($function)
                && is_a($function[0], get_class(self::$currentAutoloader));
        }

        return false;
    }

    private static function isWrappedCurrentAutoloader($function): bool
    {
        if (self::$currentAutoloader) {
            return
                is_array($function)
                && $function[0] instanceof DebugClassLoader
                && self::isCurrentAutoloader($function[0]->getClassLoader());
        }

        return false;
    }
}
