<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine\ModuleStructures;

use Doctrine\ORM\Mapping\MappedSuperclass;
use XCart\Extender\Action\Parser;
use XCart\Extender\Mapping\Extender\Depend;
use XCart\Extender\Mapping\Extender\Mixin;
use XCart\Extender\Mapping\Extender\Rely;

use function array_merge;
use function file_get_contents;
use function ltrim;
use function strpos;
use function strtolower;
use function substr;

final class Reflector
{
    private Parser $parser;

    private array $reflection = [];

    public function __construct(
        Parser $parser
    ) {
        $this->parser = $parser;
    }

    public function addEntity(string $entity, string $path): void
    {
        $this->reflection[$entity] = $this->parser->parseSource(file_get_contents($path));
    }

    public function isMixin(string $entity): bool
    {
        $reflection = $this->reflection[$entity];
        foreach ($reflection->getAnnotations() as $annotation) {
            if ($annotation instanceof Mixin) {
                return true;
            }
        }

        return false;
    }

    public function isMappedSuperclass(string $entity): bool
    {
        $reflection = $this->reflection[$entity];
        foreach ($reflection->getAnnotations() as $annotation) {
            if ($annotation instanceof MappedSuperclass) {
                return true;
            }
        }

        return false;
    }

    public function getParent(string $entity): string
    {
        $reflection = $this->reflection[$entity];

        return $this->buildFqn($reflection->getParent(), $reflection->getImports(), $reflection->getNamespace());
    }

    public function getDependencies(string $entity): array
    {
        $reflection = $this->reflection[$entity];

        $dependencies = [];
        foreach ($reflection->getAnnotations() as $annotation) {
            if (
                $annotation instanceof Depend
                || $annotation instanceof Rely
            ) {
                $dependencies[] = $annotation->dependencies;
            }
        }

        return $dependencies ? array_merge(...$dependencies) : [];
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getAliasRoot(string $name): string
    {
        $pos = strpos($name, '\\');
        if ($pos === false) {
            return $name;
        }

        return substr($name, 0, $pos);
    }

    /**
     * @param string $name
     * @param array  $imports
     * @param string $namespace
     *
     * @return string
     */
    private function buildFqn(string $name, array $imports, string $namespace): string
    {
        if ($aliasRoot = $this->getAliasRoot($name)) {
            $loweredAliasRoot = strtolower($aliasRoot);
            if (isset($imports[$loweredAliasRoot])) {
                $pos = strpos($name, '\\');

                $name = $pos === false
                    ? $imports[$loweredAliasRoot]
                    : $imports[$loweredAliasRoot] . substr($name, $pos + 1);
            } else {
                $name = $namespace . '\\' . $name;
            }
        }

        return ltrim($name, '\\');
    }
}
