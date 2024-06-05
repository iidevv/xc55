<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

final class ModuleManagerDomain
{
    private array $modules;

    private array $enabledModuleIds = [];

    public function __construct(array $modules)
    {
        $this->modules = $modules;
    }

    public function getModule(string $moduleId): ?array
    {
        return $this->modules[$moduleId] ?? null;
    }

    public function isEnabled(string $moduleId): bool
    {
        return $this->modules[$moduleId]['isEnabled'] ?? false;
    }

    public function isInstalled(string $moduleId): bool
    {
        return isset($this->modules[$moduleId]);
    }

    public function getEnabledModuleIds(): array
    {
        if (!$this->enabledModuleIds) {
            $enabledModuleIds = [];

            foreach ($this->modules as $moduleId => $module) {
                if ($module['isEnabled']) {
                    $enabledModuleIds[] = $moduleId;
                }
            }

            $this->enabledModuleIds = $enabledModuleIds;
        }

        return $this->enabledModuleIds;
    }

    public function getAllModules(): array
    {
        return $this->modules;
    }
}
