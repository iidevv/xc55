<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

declare(strict_types=1);

namespace XCart\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools\SchemaTool;
use FilesystemIterator;
use Includes\Utils\Module\Manager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use XCart\Doctrine\ModuleStructures\Reflector;
use XLite\Model\AEntity;
use XLite\Model\Base\Dump;

final class ModuleStructures
{
    private EntityManagerInterface $entityManager;

    private SchemaTool $schemaTool;

    private Reflector $reflector;

    private string $sourceRoot = '';

    private array $metadata = [];

    private array $schema = [];

    private array $entityDependencies = [];

    private array $entitiesFromFileSystem = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        SchemaTool $schemaTool,
        Reflector $reflector
    ) {
        $this->entityManager = $entityManager;
        $this->schemaTool    = $schemaTool;
        $this->reflector     = $reflector;
    }

    public function getModuleStructures(string $moduleId): array
    {
        $tables       = [];
        $columns      = [];
        $dependencies = [];

        [$entities, $mappedSuperclasses] = $this->getModuleEntities($moduleId);

        if ($mappedSuperclasses) {
            $childrenOfMappedSuperclasses = $this->getChildrenOfMappedSuperclasses($mappedSuperclasses);

            foreach ($childrenOfMappedSuperclasses as $childrenOfMappedSuperclass) {
                if ($this->reflector->isMixin($childrenOfMappedSuperclass)) {
                    continue;
                }

                $mappedSuperclass = $this->getMappedSuperclassByChild($childrenOfMappedSuperclass, $mappedSuperclasses);

                $metadata = $this->getMetadata($childrenOfMappedSuperclass);
                $table = $metadata->getTableName();

                $properties = array_filter($metadata->reflFields, static function ($property) use ($mappedSuperclass) {
                    return $property->class === $mappedSuperclass;
                });

                foreach ($properties as $property) {
                    if ($definition = $this->getColumnDefinition($childrenOfMappedSuperclass, $property->name)) {
                        $columns[$table][$property->name] = $definition;
                    }
                }
            }
        }

        // $entity is FQN of a class
        foreach ($entities as $entity) {
            if ($this->reflector->isMixin($entity)) {
                // $parent is FQN of a parent class in definition, but in real it is a final
                $parent = $this->reflector->getParent($entity);

                // class metadata from doctrine entity manager
                $metadata = $this->getMetadata($parent);

                // todo: remove prefix (?)
                $table = $metadata->getTableName();

                // mixin dependencies
                $entityDependencies = $this->reflector->getDependencies($entity);

                // get all fields
                foreach ((array) $metadata->reflFields as $field => $reflection) {
                    if (
                        // field is defined in an $entity
                        $reflection->class === $entity
                        // filed is a simple mapping
                        && !empty($metadata->fieldMappings[$field])
                        // field has a definition
                        && ($definition = $this->getColumnDefinition($parent, $field))
                    ) {
                        $columns[$table][$field] = $definition;

                        foreach ($entityDependencies as $dependency) {
                            $dependencies[$dependency][$table][$field] = $definition;
                        }
                    }
                }

                // get association mappings
                foreach ($metadata->associationMappings as $mapping) {
                    // association defined not in an $entity
                    if ($metadata->reflFields[$mapping['fieldName']]->class !== $entity) {
                        continue;
                    }

                    // join table used
                    if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                        $tables[] = $mapping['joinTable']['name'];
                        continue;
                    }

                    if (isset($mapping['joinColumns']) && $mapping['joinColumns']) {
                        foreach ($mapping['joinColumns'] as $column) {
                            if ($definition = $this->getColumnDefinition($parent, $column['name'])) {
                                $columns[$table][$column['name']] = $definition;

                                foreach ($entityDependencies as $dependency) {
                                    $dependencies[$dependency][$table][$column['name']] = $definition;
                                }
                            }
                        }
                    }
                }

                continue;
            }

            $metadata = $this->getMetadata($entity);

            $tables[] = $metadata->getTableName();

            // get association mappings
            foreach ($metadata->associationMappings as $mapping) {
                // join table used
                if (isset($mapping['joinTable']) && $mapping['joinTable']) {
                    $tables[] = $mapping['joinTable']['name'];
                }
            }
        }

        return [
            'tables'       => $tables,
            'columns'      => $columns,
            'dependencies' => $dependencies,
        ];
    }

    // return classes that represents or extends entities
    private function getModuleEntities(string $moduleId): array
    {
        [$author, $name] = explode('-', $moduleId);
        $modelPath = "{$this->sourceRoot}modules/{$author}/{$name}";
        $entities = $this->getEntitiesFromFilesystem($modelPath);

        $result = $mappedSuperclasses = [];

        $len = strlen($this->sourceRoot . 'modules/');
        foreach ($entities as $path => $item) {
            $entity = str_replace(['/src/', '/'], '\\', substr($path, $len, -4));

            if (!$this->isPersistentEntity($entity)) {
                continue;
            }

            $this->reflector->addEntity($entity, $path);

            if ($this->reflector->isMappedSuperclass($entity)) {
                $mappedSuperclasses[] = $entity;
            } else {
                $result[] = $entity;
            }
        }

        $externalDecoratedEntities = $this->getExternalDecoratedEntities($author, $name);

        return [
            array_merge($result, $externalDecoratedEntities),
            $mappedSuperclasses
        ];
    }

    private function getExternalDecoratedEntities(string $author, string $name): array
    {
        $result = [];

        foreach ($this->getEntityDependencies() as $entity => $dependencies) {
            if (in_array("{$author}\\{$name}", $dependencies, true)) {
                $result[] = $entity;
            }
        }

        return $result;
    }

    private function getEntityDependencies(): array
    {
        if (!$this->entityDependencies) {
            foreach ($this->getEnabledModulesPaths() as $modelPath => $len) {
                $entities = $this->getEntitiesFromFilesystem($modelPath);

                foreach ($entities as $path => $item) {
                    $entity = str_replace(['/src/', '/'], '\\', substr($path, $len, -4));

                    if (!$this->isPersistentEntity($entity)) {
                        continue;
                    }

                    $this->reflector->addEntity($entity, $path);
                    $dependencies = $this->reflector->getDependencies($entity);

                    if ($dependencies) {
                        $this->entityDependencies[$entity] = $dependencies;
                    }
                }
            }
        }

        return $this->entityDependencies;
    }

    private function getChildrenOfMappedSuperclasses(array $mappedSuperclasses): array
    {
        $modelsPaths = [
            "{$this->sourceRoot}classes/XLite/Model" => strlen("{$this->sourceRoot}classes/"),
        ];

        $modelsPaths = array_merge($modelsPaths, $this->getEnabledModulesPaths());

        $result = [];
        foreach ($modelsPaths as $modelsPath => $len) {
            $entities = $this->getEntitiesFromFilesystem($modelsPath);

            foreach ($entities as $path => $item) {
                $entity = str_replace(['/src/', '/'], '\\', substr($path, $len, -4));

                if (!$this->isPersistentEntity($entity)) {
                    continue;
                }

                $this->reflector->addEntity($entity, $path);

                if ($this->getMappedSuperclassByChild($entity, $mappedSuperclasses)) {
                    //$this->reflector->addEntity($entity, $path);
                    $result[] = $entity;
                }
            }
        }

        return $result;
    }

    private function getEntitiesFromFilesystem(string $entitiesPath): array
    {
        if (!isset($this->entitiesFromFileSystem[$entitiesPath])) {
            $iterator = new RecursiveDirectoryIterator(
                $entitiesPath,
                FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME
            );

            $iterator = new RecursiveIteratorIterator($iterator);

            /*
                Filter paths by pattern
                include paths like:
                    .../modules/.../Model/....*php
                    .../modules/.../Module/.../Model/....*php
                exclude paths like:
                    .../modules/.../View/Model/....*php
                    .../modules/.../Module/.../View/Model/....*php
            */
            $iterator = new RegexIterator(
                $iterator,
                '/^.*(?<!\/View)\/Model\/.+\.php$/S',
                RegexIterator::GET_MATCH
            );

            $this->entitiesFromFileSystem[$entitiesPath] = iterator_to_array($iterator);
        }

        return $this->entitiesFromFileSystem[$entitiesPath];
    }

    private function getEnabledModulesPaths(): array
    {
        $enabledModuleIds = Manager::getRegistry()->getEnabledModuleIds();
        $externalModelPaths = [];

        $len = strlen("{$this->sourceRoot}modules/");
        foreach ($enabledModuleIds as $enabledModuleId) {
            [$enabledModuleAuthor, $enabledModuleName] = explode('-', $enabledModuleId);
            $externalModelPaths["{$this->sourceRoot}modules/{$enabledModuleAuthor}/{$enabledModuleName}"] = $len;
        }

        return $externalModelPaths;
    }

    private function getMappedSuperclassByChild(string $entity, array $superClasses): ?string
    {
        foreach ($superClasses as $superClass) {
            if (is_subclass_of($entity, $superClass)) {
                return $superClass;
            }
        }

        return null;
    }

    private function isPersistentEntity(string $entity): bool
    {
        return (
            class_exists($entity)
            && is_subclass_of($entity, AEntity::class)
            && !is_subclass_of($entity, Dump::class)
        );
    }

    private function getMetadata(string $entity): ClassMetadata
    {
        if (!isset($this->metadata[$entity])) {
            $this->metadata[$entity] = $this->entityManager->getClassMetadata($entity);
        }

        return $this->metadata[$entity];
    }

    private function getColumnDefinition(string $entity, string $column): string
    {
        if (!isset($this->schema[$entity])) {
            $metadata              = $this->getMetadata($entity);
            $this->schema[$entity] = $this->schemaTool->getCreateSchemaSql([$metadata])[0];
        }

        $pattern = '/(?:, |\()(' . $column . ' .+?)(?:, [A-Za-z]|\) ENGINE)/Ssi';

        if (preg_match($pattern, $this->schema[$entity], $matches)) {
            return $matches[1];
        }

        return '';
    }

    public function getSourceRoot(): string
    {
        return $this->sourceRoot;
    }

    public function setSourceRoot(string $sourceRoot): void
    {
        $this->sourceRoot = $sourceRoot;
    }
}
