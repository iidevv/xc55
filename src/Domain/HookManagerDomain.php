<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Domain;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\EventDispatcher\Event;
use XCart\Exception\HookManagerException;

final class HookManagerDomain
{
    public const CORE_MODULE_ID = 'CDev-Core';

    public const HOOK_TYPE_INIT    = 'init';
    public const HOOK_TYPE_INSTALL = 'install';
    public const HOOK_TYPE_ENABLE  = 'enable';
    public const HOOK_TYPE_DISABLE = 'disable';
    public const HOOK_TYPE_REBUILD = 'rebuild';
    public const HOOK_TYPE_REMOVE  = 'remove';
    public const HOOK_TYPE_UPGRADE = 'upgrade';

    private EntityManagerInterface $entityManager;

    private ModuleManagerDomain $moduleManagerDomain;

    private array $moduleRelatedEntityClasses = [];

    private array $hooks = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        ModuleManagerDomain $moduleManagerDomain
    ) {
        $this->entityManager       = $entityManager;
        $this->moduleManagerDomain = $moduleManagerDomain;
    }

    /**
     * @throws HookManagerException
     */
    public function addHook(array $hook): void
    {
        $object   = $hook['object'] ?? null;
        $moduleId = $hook['moduleId'] ?? '';
        $hookType = $hook['hookType'] ?? '';

        if (!$moduleId) {
            throw HookManagerException::fromEmptyModuleId(
                get_class($object)
            );
        }

        if (!$hookType) {
            throw HookManagerException::fromEmptyHookType(
                get_class($object),
                $moduleId
            );
        }

        $this->hooks[$moduleId][$hookType][] = [
            'object'  => $object,
            'method'  => $hook['method'],
            'version' => $hook['version'] ?? '',
        ];
    }

    /**
     * @throws HookManagerException
     */
    public function runHook(array $hook): int
    {
        $hookType = $hook['hookType'] ?? '';
        $moduleId = $hook['moduleId'] ?? '';

        $result = 0;

        try {
            switch ($hookType) {
                case self::HOOK_TYPE_UPGRADE:
                    $result = $this->runUpgradeHook($moduleId, $hook['versionFrom'], $hook['versionTo']);
                    break;
                case self::HOOK_TYPE_REMOVE:
                    $result = $this->runRemoveHook($moduleId);
                    break;
                case self::HOOK_TYPE_DISABLE:
                    $result = $this->runDisableHook($moduleId);
                    break;
                case self::HOOK_TYPE_INIT:
                case self::HOOK_TYPE_INSTALL:
                case self::HOOK_TYPE_ENABLE:
                case self::HOOK_TYPE_REBUILD:
                    $result = $this->runCommonHook(
                        $moduleId,
                        $hookType,
                        $hook['event'] ?? null
                    );
                    break;
            }
        } catch (\Exception $e) {
            throw HookManagerException::fromRunHook($e, $moduleId, $hookType);
        }

        return $result;
    }

    private function runCommonHook(
        string $moduleId,
        string $hookType,
        ?Event $event = null
    ): int {
        $hooks = $this->hooks[$moduleId][$hookType] ?? [];

        foreach ($hooks as $hook) {
            $object = $hook['object'];
            $method = $hook['method'];

            $object->$method($event);
        }

        return count($hooks);
    }

    private function runUpgradeHook(string $moduleId, string $versionFrom, string $versionTo): int
    {
        if (isset($this->hooks[$moduleId][self::HOOK_TYPE_UPGRADE])) {
            $upgradeHooks = array_filter(
                $this->hooks[$moduleId][self::HOOK_TYPE_UPGRADE],
                static fn ($upgradeHook) => version_compare($upgradeHook['version'], $versionFrom, '>') && version_compare($upgradeHook['version'], $versionTo, '<=')
            );

            usort($upgradeHooks, [$this, 'sortByVersion']);

            foreach ($upgradeHooks as $upgradeHook) {
                $object = $upgradeHook['object'];
                $method = $upgradeHook['method'];

                $object->$method();
            }

            return count($upgradeHooks);
        }

        return 0;
    }

    private function sortByVersion($hook1, $hook2): int
    {
        return version_compare($hook1['version'], $hook2['version']);
    }

    private function runRemoveHook(string $moduleId): int
    {
        $module     = $this->moduleManagerDomain->getModule($moduleId);
        $canDisable = $module['canDisable'] ?? true;

        $result = $canDisable
            ? 0
            : $this->runCommonHook($moduleId, self::HOOK_TYPE_REMOVE);

        if (!$this->moduleRelatedEntityClasses) {
            $this->setModuleRelatedEntityClasses();
        }

        foreach ($this->moduleRelatedEntityClasses as $entityClass) {
            [$author, $name] = explode('-', $moduleId);

            $this->entityManager
                ->getRepository($entityClass)
                ->removeByModule("{$author}\\{$name}");
        }

        return $result;
    }

    private function runDisableHook(string $moduleId): int
    {
        $result = $this->runCommonHook($moduleId, self::HOOK_TYPE_DISABLE);

        [$author, $name] = explode('-', $moduleId);

        $this->entityManager
            ->getRepository(\XLite\Model\Notification::class)
            ->disableNotificationsByModule("{$author}\\{$name}");

        return $result;
    }

    private function setModuleRelatedEntityClasses()
    {
        foreach ($this->entityManager->getMetadataFactory()->getAllMetadata() as $metadata) {
            $entityClass = $metadata->getName();
            $interfaces  = class_implements($entityClass);

            if (isset($interfaces[\XLite\Model\Base\IModuleRelatedEntity::class])) {
                $this->moduleRelatedEntityClasses[] = $entityClass;
            }
        }
    }

    /**
     * @return array
     */
    public function getHooks(): array
    {
        return $this->hooks;
    }

    /**
     * @param array $hooks
     */
    public function setHooks(array $hooks): void
    {
        $this->hooks = $hooks;
    }
}
