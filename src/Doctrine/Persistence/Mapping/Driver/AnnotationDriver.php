<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\Doctrine\Persistence\Mapping\Driver;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Doctrine\Persistence\Mapping\MappingException;
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use XCart\Framework\AutoloaderManager;

use function array_merge;
use function array_unique;
use function assert;
use function get_class;
use function get_declared_classes;
use function in_array;
use function is_dir;
use function preg_match;
use function preg_quote;
use function realpath;
use function str_replace;
use function strpos;

/**
 * It is a copy of the class \Doctrine\Persistence\Mapping\Driver\AnnotationDriver (doctrine/persistence 2.3.0)
 * This class is injected by aliasing the original class. (@see class_alias())
 * This solution is required for development mode
 *
 * @see \App\Operation\Build\GenerateAutoloader (service-tool)
 * @see /src/vendor/autoload_xcart.php
 */
abstract class AnnotationDriver implements MappingDriver
{
    /**
     * The annotation reader.
     *
     * @var Reader
     */
    protected $reader;

    /**
     * The paths where to look for mapping files.
     *
     * @var string[]
     */
    protected $paths = [];

    /**
     * The paths excluded from path where to look for mapping files.
     *
     * @var string[]
     */
    protected $excludePaths = [];

    /**
     * The file extension of mapping documents.
     *
     * @var string
     */
    protected $fileExtension = '.php';

    /**
     * Cache for AnnotationDriver#getAllClassNames().
     *
     * @var string[]|null
     * @psalm-var list<class-string>|null
     */
    protected $classNames;

    /**
     * Name of the entity annotations as keys.
     *
     * @var array<class-string, bool|int>
     */
    protected $entityAnnotationClasses = [];

    /**
     * Initializes a new AnnotationDriver that uses the given AnnotationReader for reading
     * docblock annotations.
     *
     * @param Reader               $reader The AnnotationReader to use, duck-typed.
     * @param string|string[]|null $paths  One or multiple paths where mapping classes can be found.
     */
    public function __construct($reader, $paths = null)
    {
        $this->reader = $reader;
        if (! $paths) {
            return;
        }

        $this->addPaths((array) $paths);
    }

    /**
     * Appends lookup paths to metadata driver.
     *
     * @param string[] $paths
     *
     * @return void
     */
    public function addPaths(array $paths)
    {
        $this->paths = array_unique(array_merge($this->paths, $paths));
    }

    /**
     * Retrieves the defined metadata lookup paths.
     *
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Append exclude lookup paths to metadata driver.
     *
     * @param string[] $paths
     *
     * @return void
     */
    public function addExcludePaths(array $paths)
    {
        $this->excludePaths = array_unique(array_merge($this->excludePaths, $paths));
    }

    /**
     * Retrieve the defined metadata lookup exclude paths.
     *
     * @return string[]
     */
    public function getExcludePaths()
    {
        return $this->excludePaths;
    }

    /**
     * Retrieve the current annotation reader
     *
     * @return Reader
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * Gets the file extension used to look for mapping files under.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Sets the file extension used to look for mapping files under.
     *
     * @param string $fileExtension The file extension to set.
     *
     * @return void
     */
    public function setFileExtension($fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    /**
     * Returns whether the class with the specified name is transient. Only non-transient
     * classes, that is entities and mapped superclasses, should have their metadata loaded.
     *
     * A class is non-transient if it is annotated with an annotation
     * from the {@see AnnotationDriver::entityAnnotationClasses}.
     *
     * @return bool
     */
    public function isTransient($className)
    {
        $classAnnotations = $this->reader->getClassAnnotations(new ReflectionClass($className));

        foreach ($classAnnotations as $annot) {
            if (isset($this->entityAnnotationClasses[get_class($annot)])) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllClassNames(): array
    {
        if ($this->classNames !== null) {
            return $this->classNames;
        }

        if (! $this->paths) {
            throw MappingException::pathRequired();
        }

        $classes       = [];
        $includedFiles = [];

        foreach ($this->paths as $path) {
            if (! is_dir($path)) {
                throw MappingException::fileMappingDriversRequireConfiguredDirectoryPath($path);
            }

            $iterator = new RegexIterator(
                new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::LEAVES_ONLY
                ),
                '/^.+' . preg_quote($this->fileExtension) . '$/i',
                RecursiveRegexIterator::GET_MATCH
            );

            foreach ($iterator as $file) {
                $sourceFile = $file[0];

                if (! preg_match('(^phar:)i', $sourceFile)) {
                    $sourceFile = realpath($sourceFile);
                }

                foreach ($this->excludePaths as $excludePath) {
                    $realExcludePath = realpath($excludePath);
                    assert($realExcludePath !== false);
                    $exclude = str_replace('\\', '/', $realExcludePath);
                    $current = str_replace('\\', '/', $sourceFile);

                    if (strpos($current, $exclude) !== false) {
                        continue 2;
                    }
                }

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

                $includedFiles[] = $sourceFile;
            }
        }

        $declared = get_declared_classes();

        foreach ($declared as $className) {
            $rc         = new ReflectionClass($className);
            $sourceFile = $rc->getFileName();
            if (! in_array($sourceFile, $includedFiles) || $this->isTransient($className)) {
                continue;
            }

            $classes[] = $className;
        }

        $this->classNames = $classes;

        return $classes;
    }
}
