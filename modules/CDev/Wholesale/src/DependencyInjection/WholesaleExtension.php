<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace CDev\Wholesale\DependencyInjection;

use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class WholesaleExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    /**
     * @throws Exception
     */
    public function prepend(ContainerBuilder $container): void
    {
        if ($container->hasDefinition('XC\ProductVariants\API\OpenApiTagsRepositoryDecorator')) {
            $loader = new YamlFileLoader($container, new FileLocator(LC_DIR_MODULES . 'CDev/Wholesale/config'));
            $loader->load('product_variants_services.yaml');
        }
    }
}
