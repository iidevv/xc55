<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine\FixtureLoader;

use Doctrine\ORM\EntityManagerInterface;

final class YamlLoader
{
    private EntityManagerInterface $entityManager;

    private YamlLoadingOptions $options;

    private string $module = '';

    private array $allowedModels = [];

    private array $excludedModels = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        YamlLoadingOptions $options
    ) {
        $this->entityManager = $entityManager;
        $this->options = $options;
    }

    public function loadYaml(?array $data): void
    {
        foreach ($data ?: [] as $entityName => $datum) {
            /** @var \XLite\Model\Repo\ARepo $repository */
            if (
                $this->isEntityAllowed($entityName)
                && ($repository = $this->entityManager->getRepository($entityName))
            ) {
                $rows = $this->detectOptions($datum);

                $repository->loadFixtures($rows);

                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
    }

    // todo: move to modules package
    public function detectModuleFromPath(string $path): string
    {
        if (preg_match('#modules/(\w+)/(\w+)/#S', $path, $matches)) {
            return "{$matches[1]}-{$matches[2]}";
        }

        return '';
    }

    public function detectOptions(array $data): array
    {
        [$directives, $data] = $this->getDirectives($data);

        $this->options->reset();

        $this->options->setOption('insert', $directives['forceInsertCommand'] ?? false);
        $this->options->setOption('addModel', $directives['allowedModel'] ?? null);
        $this->options->setOption('addParent', $directives['allowMissingParentInsert'] ?? true);

        $this->options->setOption('moduleName', $this->module);

        return $data;
    }

    public function getDirectives(array $data): array
    {
        $directives = $data['directives'] ?? null;
        unset($data['directives']);

        if (!$directives) {
            foreach ($data as $index => $datum) {
                if (isset($datum['directives'])) {
                    $directives = $datum['directives'];

                    unset($data[$index]);
                    break;
                }
            }
        }

        return [$directives, array_values($data)];
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    public function getAllowedModels(): array
    {
        return $this->allowedModels;
    }

    public function setAllowedModels(array $allowedModels): void
    {
        $this->allowedModels = $allowedModels;
    }

    public function getExcludedModels(): array
    {
        return $this->excludedModels;
    }

    public function setExcludedModels(array $excludedModels): void
    {
        $this->excludedModels = $excludedModels;
    }

    private function isEntityAllowed(string $entity): bool
    {
        $isAllowed = !$this->allowedModels || in_array($entity, $this->allowedModels, true);
        $isExcluded = $this->excludedModels && in_array($entity, $this->excludedModels, true);

        return $isAllowed && !$isExcluded;
    }
}
