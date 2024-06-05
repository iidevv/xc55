<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine;

use Doctrine\DBAL\Driver\Exception as DriverException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Throwable;
use XCart\Doctrine\FixtureLoader\SQLLoader;
use XCart\Doctrine\FixtureLoader\YamlLoader;

final class FixtureLoader
{
    private SQLLoader $SQLLoader;

    private YamlLoader $yamlLoader;

    private Filesystem $filesystem;

    private LoggerInterface $logger;

    private string $tablePrefix;

    public function __construct(
        SQLLoader $SQLLoader,
        YamlLoader $yamlLoader,
        Filesystem $filesystem,
        LoggerInterface $logger,
        string $tablePrefix
    ) {
        $this->SQLLoader   = $SQLLoader;
        $this->yamlLoader  = $yamlLoader;
        $this->filesystem  = $filesystem;
        $this->logger      = $logger;
        $this->tablePrefix = $tablePrefix;
    }

    /**
     * @throws DriverException
     * @throws DBALException
     * @throws Throwable
     */
    public function loadSQL(string $filePath): int
    {
        if (!$this->filesystem->exists($filePath)) {
            $this->logger->warning(
                "The file $filePath doesn't exist.",
                ['method' => __METHOD__]
            );

            return 0;
        }

        $file    = new SplFileInfo($filePath, '', '');
        $content = $file->getContents();

        $content = str_replace('%%XC%%_', $this->tablePrefix . '_', $content);

        return $this->SQLLoader->loadSQL($content);
    }

    public function loadYaml(
        string $filePath,
        array $allowedModels = [],
        array $excludedModels = [],
        array $params = []
    ): void {
        if (!$this->filesystem->exists($filePath)) {
            $this->logger->warning(
                "The file $filePath doesn't exist.",
                ['method' => __METHOD__]
            );

            return;
        }

        $file = new SplFileInfo($filePath, '', '');

        $content = str_replace(
            array_keys($params),
            array_values($params),
            $file->getContents()
        );

        $module = $this->yamlLoader->detectModuleFromPath($filePath);

        $this->yamlLoader->setModule($module);
        $this->yamlLoader->setAllowedModels($allowedModels);
        $this->yamlLoader->setExcludedModels($excludedModels);

        $this->yamlLoader->loadYaml(Yaml::parse($content));

        // todo: reset method
        $this->yamlLoader->setModule('');
        $this->yamlLoader->setAllowedModels([]);
        $this->yamlLoader->setExcludedModels([]);
    }
}
