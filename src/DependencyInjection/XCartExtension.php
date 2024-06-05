<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;
use XCart\Domain\StaticConfigDomain;

class XCartExtension extends Extension implements CompilerPassInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $original = $this->processConfiguration($configuration, [$configs[0]]);

        /** @var array $config */
        $config = $container->resolveEnvPlaceholders($config);

        $config['host_details']['http_host_orig'] = $config['host_details']['http_host'];
        $config['host_details']['https_host_orig'] = $config['host_details']['https_host'];

        /** @var array $original */
        $original = $container->resolveEnvPlaceholders($original);

        $original['host_details']['http_host_orig'] = $original['host_details']['http_host'];
        $original['host_details']['https_host_orig'] = $original['host_details']['https_host'];

        $container->register(StaticConfigDomain::class, StaticConfigDomain::class)
            ->setArguments([$config, $original])
            ->setPublic(true);
    }

    public function process(ContainerBuilder $container): void
    {
        // change reader for api resources metadata factory
        $definition = $container->getDefinition('api_platform.metadata.resource.metadata_factory.annotation');
        $definition->setArgument(0, new Reference('xcart.api_resources.reader'));
    }
}
