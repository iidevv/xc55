<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\Common\Annotations;

use ReflectionClass;
use ReflectionFunction;
use SplFileObject;
use XCart\Framework\AutoloaderManager;

use function is_file;
use function method_exists;
use function preg_quote;
use function preg_replace;

/**
 * It is a copy of the class \Doctrine\Common\Annotations\PhpParser (doctrine/common 3.2.1)
 * This class is injected by aliasing the original class. (@see class_alias())
 * This solution is required for development mode
 *
 * @see \App\Operation\Build\GenerateAutoloader (service-tool)
 * @see /src/vendor/autoload_xcart.php
 */
final class PhpParser
{
    /**
     * Parses a class.
     *
     * @deprecated use parseUseStatements instead
     *
     * @param ReflectionClass $class A <code>ReflectionClass</code> object.
     *
     * @return array<string, class-string> A list with use statements in the form (Alias => FQN).
     */
    public function parseClass(ReflectionClass $class)
    {
        return $this->parseUseStatements($class);
    }

    /**
     * Parse a class or function for use statements.
     *
     * @param ReflectionClass|ReflectionFunction $reflection
     *
     * @psalm-return array<string, string> a list with use statements in the form (Alias => FQN).
     */
    public function parseUseStatements($reflection): array
    {
        if (method_exists($reflection, 'getUseStatements')) {
            return $reflection->getUseStatements();
        }

        /* Original code:
        $filename = $reflection->getFileName();

        if ($filename === false) {
            return [];
        }
        */

        /* Modified code : */
        $filename = AutoloaderManager::getCurrentAutoloader()->getTargetFilePath($reflection->getName()) ?: $reflection->getFileName();

        if (empty($filename)) {
            return [];
        }
        /* End of modified code */

        $content = $this->getFileContent($filename, $reflection->getStartLine());

        if ($content === null) {
            return [];
        }

        $namespace = preg_quote($reflection->getNamespaceName());
        $content   = preg_replace('/^.*?(\bnamespace\s+' . $namespace . '\s*[;{].*)$/s', '\\1', $content);
        $tokenizer = new \Doctrine\Common\Annotations\TokenParser('<?php ' . $content);

        return $tokenizer->parseUseStatements($reflection->getNamespaceName());
    }

    /**
     * Gets the content of the file right up to the given line number.
     *
     * @param string $filename   The name of the file to load.
     * @param int    $lineNumber The number of lines to read from file.
     *
     * @return string|null The content of the file or null if the file does not exist.
     */
    private function getFileContent($filename, $lineNumber)
    {
        if (! is_file($filename)) {
            return null;
        }

        $content = '';
        $lineCnt = 0;
        $file    = new SplFileObject($filename);
        while (! $file->eof()) {
            if ($lineCnt++ === $lineNumber) {
                break;
            }

            $content .= $file->fgets();
        }

        return $content;
    }
}
