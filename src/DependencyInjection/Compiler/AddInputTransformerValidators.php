<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart\DependencyInjection\Compiler;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use XLite\API\Validation\InputTransformerInitializerValidationDecorator;
use XLite\API\Validation\InputTransformerValidationDecorator;

final class AddInputTransformerValidators implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('api_platform.data_transformer') as $id => $tag) {
            $definition = $container->getDefinition($id);
            if (!$this->isInputTransformer($definition) || !$definition->getClass() || $definition->isAbstract()) {
                continue;
            }

            $validatorClass = is_a($definition->getClass(), DataTransformerInitializerInterface::class, true)
                ? InputTransformerInitializerValidationDecorator::class
                : InputTransformerValidationDecorator::class;

            $decoratorId = $id . '.decorator.validation';
            $container->register($decoratorId, $validatorClass)
                ->setDecoratedService($id)
                ->setPublic(true)
                ->setAutowired(true);
        }
    }

    protected function isInputTransformer(Definition $service): bool
    {
        return $service->getClass()
            && strpos($service->getClass(), "Input") !== false;
    }
}
