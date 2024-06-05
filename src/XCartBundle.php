<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XCart;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use XCart\DependencyInjection\Compiler\AddInputTransformerValidators;
use XCart\DependencyInjection\Compiler\AddModuleLifetimeHooksPass;
use XCart\DependencyInjection\Compiler\AddTemplatePathPass;

class XCartBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddInputTransformerValidators());
        $container->addCompilerPass(new AddTemplatePathPass());
        $container->addCompilerPass(new AddModuleLifetimeHooksPass());
    }
}
