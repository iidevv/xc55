<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use XLite\Core\Layout;

class AddTemplatePathPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $projectDir = $container->getParameter('kernel.project_dir');
        $paths = $container->getParameter('xcart.skin_model');

        $twigFilesystemLoaderDefinition = $container->getDefinition('twig.loader.native_filesystem');

        foreach ($paths as $interface => $config) {
            foreach ($config as $zone => $paths) {
                foreach ($paths as $path) {
                    $templatePath = str_replace(Layout::PATH_PATTERN, Layout::TEMPLATES_PATH, $path);

                    if (is_dir($projectDir . '/' . $templatePath)) {
                        $twigFilesystemLoaderDefinition->addMethodCall('addPath', [$templatePath, "{$interface}.{$zone}"]);
                    }
                }
            }
        }

        // Remove default form theme because we use custom themes
        $twigFormResources = $container->getParameter('twig.form.resources');
        $container->setParameter('twig.form.resources', array_values(array_filter($twigFormResources, static fn ($item) => $item !== 'form_div_layout.html.twig')));
    }
}
