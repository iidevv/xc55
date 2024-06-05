<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace Qualiteam\SkinActMain;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SkinActMainBundle extends Bundle
{
    protected array $customParameters = [
        'getModulePath',
    ];

    /**
     * {@inheritdoc}
     *
     * This method can be overridden to register compilation passes,
     * other extensions, ...
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        foreach ($this->customParameters as $param) {
            $container->setParameter($this->prepareParamName($param), $this->prepareParamValue($param));
        }
    }

    /**
     * @param string $paramName
     *
     * @return string
     */
    protected function prepareParamName(string $paramName): string
    {
        [$author, $name] = explode('\\', $this->getNamespace());

        return sprintf('%s.%s.%s',
            $author,
            $name,
            $paramName
        );
    }

    /**
     * @param string $param
     *
     * @return string
     */
    protected function prepareParamValue(string $param): string
    {
        return $this->{'getParamValue' . ucfirst($param)}();
    }

    /**
     * @return string
     */
    protected function getParamValueGetModulePath(): string
    {
        [$author, $name] = explode('\\', $this->getNamespace());

        return sprintf('%s/%s/%s/',
            'modules',
            $author,
            $name
        );
    }
}
