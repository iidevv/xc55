<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use XCart\Domain\HookManagerDomain;

class AddModuleLifetimeHooksPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $hookManagerDomainService = $container->getDefinition(HookManagerDomain::class);

        foreach ($container->findTaggedServiceIds('xcart.lifetime-hook', true) as $serviceId => $tags) {
            foreach ($tags as $tag) {
                $hookType = $tag['type'] ?? '';

                $hook = [
                    'moduleId' => $tag['moduleId'] ?? $this->getModuleIdByServiceDefinition($container->getDefinition($serviceId)),
                    'hookType' => $hookType,
                    'object'   => new Reference($serviceId),
                    'method'   => $tag['method'] ?? "on{$hookType}",
                ];

                if ($hookType === HookManagerDomain::HOOK_TYPE_UPGRADE) {
                    $hook['version'] = $tag['version'] ?? '';
                }

                $hookManagerDomainService->addMethodCall('addHook', [$hook]);
            }
        }
    }

    private function getModuleIdByServiceDefinition(Definition $serviceDefinition): string
    {
        $class = $serviceDefinition->getClass();

        if (strpos($class, 'XCart') === 0) {
            return HookManagerDomain::CORE_MODULE_ID;
        }

        $namespaceParts = explode('\\', $class);

        if (count($namespaceParts) > 2) {
            return "{$namespaceParts[0]}-{$namespaceParts[1]}";
        }

        return '';
    }
}
