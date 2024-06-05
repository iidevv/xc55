<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Framework\ApiPlatform\Core\Util;

use XCart\Framework\AutoloaderManager;

/**
 * It is a copy of the class \ApiPlatform\Core\Util\ReflectionClassRecursiveIterator (api-platform/core v2.6.8)
 * This class is injected by aliasing the original class. (@see class_alias())
 * This solution is required to avoid double class declaration in development mode
 *
 * @see \App\Operation\Build\GenerateAutoloader (service-tool)
 * @see /src/vendor/autoload_xcart.php
 */
final class ReflectionClassRecursiveIterator
{
    private function __construct()
    {
    }

    public static function getReflectionClassesFromDirectories(array $directories): \Iterator
    {
        foreach ($directories as $path) {
            $iterator = new \RegexIterator(
                new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+\.php$/i',
                \RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (!preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                try {
                    /* Original code:

                    require_once $sourceFile;

                    */

                    /* Modified code: */

                    // In XC development autoloader class may be included by fake path so *_once is not a solution
                    // Check for the class existence manually it triggers autoloader
                    if (
                        preg_match('#var/run/classes/(.*)\.php#', $sourceFile, $matches)
                        && ($name = str_replace('/', '\\', $matches[1]))
                        && (class_exists($name) || interface_exists($name) || trait_exists($name))
                    ) {
                        $sourceFile = AutoloaderManager::getCurrentAutoloader()->getSourceFilePath($name);
                    } else {
                        require_once $sourceFile;
                    }

                    /* End of modified code */
                } catch (\Throwable $t) {
                    // invalid PHP file (example: missing parent class)
                    continue;
                }

                $includedFiles[$sourceFile] = true;
            }
        }

        $declared = array_merge(get_declared_classes(), get_declared_interfaces());
        foreach ($declared as $className) {
            $reflectionClass = new \ReflectionClass($className);
            $sourceFile = $reflectionClass->getFileName();
            if (isset($includedFiles[$sourceFile])) {
                yield $className => $reflectionClass;
            }
        }
    }
}
